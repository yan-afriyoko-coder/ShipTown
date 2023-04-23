<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Events\OrderProductShipmentCreatedEvent;
use App\Http\Resources\InventoryMovementResource;
use App\Http\Resources\OrderProductShipmentResource;
use App\Models\InventoryMovement;
use App\Models\OrderProductShipment;
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
class PublishOrderProductShipmentWebhooksJob implements ShouldQueue
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
                $this->publishOrderProductShipmentsWebhooks($warehouse->id);
            });
    }

    private function publishOrderProductShipmentsWebhooks(int $warehouse_id)
    {
        $query = PendingWebhook::query()
            ->selectRaw('modules_webhooks_pending_webhooks.*')
            ->leftJoin('orders_products_shipments as ops', 'ops.id', '=', 'modules_webhooks_pending_webhooks.model_id')
            ->where([
                'model_class' => OrderProductShipment::class,
                'reserved_at' => null,
                'published_at' => null,
                'ops.warehouse_id' => $warehouse_id,
            ])
            ->orderBy('modules_webhooks_pending_webhooks.id')
            ->limit(100);

        $chunk = $query->get();

        while ($chunk->isNotEmpty()) {
            $pendingWebhookIds = $chunk->pluck('id');

            try {
                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update(['reserved_at' => now()]);

                $this->publishRecords($chunk);

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
    private function publishRecords($chunk): void
    {
        $records = OrderProductShipmentResource::collection(
            OrderProductShipment::query()
                ->whereIn('id', $chunk->pluck('model_id'))
                ->orderBy('id')
                ->with(['order', 'orderProduct', 'warehouse', 'user', 'product'])
                ->get()
        );

        $payload = collect(['OrderProductShipments' => $records]);

        SnsService::publishNew(
            $payload->toJson(),
            [
                "warehouse_code" => [
                    "DataType" => "String",
                    "StringValue" => data_get($records->collection->first()->resource->toArray(), 'warehouse.code')
                ]
            ]
        );
    }
}
