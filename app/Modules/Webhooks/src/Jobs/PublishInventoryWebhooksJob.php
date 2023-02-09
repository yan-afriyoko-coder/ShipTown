<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Http\Resources\InventoryResource;
use App\Http\Resources\OrderResource;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Warehouse;
use App\Modules\Webhooks\src\Models\PendingWebhook;
use App\Modules\Webhooks\src\Services\SnsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishInventoryWebhooksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        Warehouse::query()
            ->get('code')
            ->each(function (Warehouse $warehouse) {
                $this->publishInventoryWebhooks($warehouse->code);
            });
    }

    private function publishInventoryWebhooks(string $warehouse_code): void
    {
        $query = PendingWebhook::query()
            ->selectRaw('modules_webhooks_pending_webhooks.*')
            ->leftJoin('inventory', 'inventory.id', '=', 'modules_webhooks_pending_webhooks.model_id')
            ->where([
                'model_class' => Inventory::class,
                'reserved_at' => null,
                'published_at' => null,
                'inventory.warehouse_code' => $warehouse_code,
            ])
            ->orderBy('modules_webhooks_pending_webhooks.id')
            ->limit(100);

        do {
            $chunk = $query->get();

            if ($chunk->isEmpty()) {
                break;
            }

            $pendingWebhookIds = $chunk->pluck('id');

            try {
                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update(['reserved_at' => now()]);

                $this->publishInventoryMessage($chunk);

                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update(['published_at' => now()]);
            } catch (Exception $exception) {
                PendingWebhook::query()
                    ->whereIn('id', $pendingWebhookIds)
                    ->update(['reserved_at' => null, 'published_at' => null]);

                throw $exception;
            }

            // Sleep for 1 second to avoid hitting the SNS rate limit
            sleep(1);
        } while ($chunk->isNotEmpty());
    }

    private function publishInventoryMessage($chunk): void
    {
        $ordersCollection = InventoryResource::collection(
            Inventory::query()
                ->whereIn('id', $chunk->pluck('model_id'))
                ->orderBy('id')
                ->with(['product', 'warehouse'])
                ->get()
        );

        $payload = collect(['Inventory' => $ordersCollection]);

        SnsService::publishNew(
            $payload->toJson(),
            [
                "warehouse_code" => [
                    "DataType" => "String",
                    "StringValue" => data_get($ordersCollection->collection->first()->resource->toArray(), 'warehouse.code')
                ]
            ]
        );
    }
}
