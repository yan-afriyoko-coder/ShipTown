<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Http\Resources\InventoryMovementResource;
use App\Models\InventoryMovement;
use App\Models\Warehouse;
use App\Modules\Webhooks\src\Models\PendingWebhook;
use App\Modules\Webhooks\src\Services\SnsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishInventoryMovementWebhooksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        Warehouse::query()
            ->get('id')
            ->each(function (Warehouse $warehouse) {
                $this->publishWarehouseWebhooks($warehouse->getKey());
            });
    }

    private function publishWarehouseWebhooks(int $warehouse_id)
    {
        $query = PendingWebhook::query()
            ->selectRaw('modules_webhooks_pending_webhooks.*')
            ->leftJoin(
                'inventory_movements',
                'inventory_movements.id',
                '=',
                'modules_webhooks_pending_webhooks.model_id'
            )
            ->where([
                'model_class' => InventoryMovement::class,
                'reserved_at' => null,
                'published_at' => null,
                'inventory_movements.warehouse_id' => $warehouse_id,
            ])
            ->orderBy('id')
            ->limit(100);

        $chunk = $query->get();

        while ($chunk->isNotEmpty()) {
            $pendingWebhookIds = $chunk->pluck('id');

            try {
                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update(['reserved_at' => now()]);

                $this->publishInventoryMovementMessage($chunk);

                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update(['published_at' => now()]);
            } catch (Exception $exception) {
                PendingWebhook::query()
                    ->whereIn('id', $pendingWebhookIds)
                    ->update(['reserved_at' => null, 'published_at' => null]);

                throw $exception;
            }

            $chunk = $query->get();
        }
    }

    /**
     * @param $chunk
     */
    private function publishInventoryMovementMessage($chunk): void
    {
        $inventoryMovementsCollection = InventoryMovementResource::collection(
            InventoryMovement::query()
                ->whereIn('id', $chunk->pluck('model_id'))
                ->orderBy('id')
                ->with(['product', 'warehouse', 'user'])
                ->get()
        );

        $payload = collect(['InventoryMovements' => $inventoryMovementsCollection]);

        SnsService::publishNew(
            $payload->toJson(),
            [
                "warehouse_code" => [
                  "DataType" => "String",
                  "StringValue" => data_get($inventoryMovementsCollection->collection->first()->resource->toArray(), 'warehouse.code')
                ]
            ]
        );
    }
}
