<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Http\Resources\OrderProductShipmentResource;
use App\Models\OrderProductShipment;
use App\Models\Warehouse;
use App\Modules\Webhooks\src\Models\PendingWebhook;
use App\Modules\Webhooks\src\Models\Webhook;
use App\Modules\Webhooks\src\Services\SnsService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishOrderProductShipmentWebhooksJob extends UniqueJob
{
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
        Log::debug('Publishing OrderProductShipment webhooks for warehouse_id: ' . $warehouse_id);

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
            ->limit(50);

        $chunk = $query->get();

        while ($chunk->isNotEmpty()) {
            $pendingWebhookIds = $chunk->pluck('id');

            try {
                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update(['reserved_at' => now()]);

                $response = $this->publishRecords($chunk);

                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update([
                    'published_at' => now(),
                    'sns_message_id' => $response->get('MessageId'),
                ]);
            } catch (Exception $exception) {
                PendingWebhook::query()
                    ->whereIn('id', $pendingWebhookIds)
                    ->update(['reserved_at' => null, 'published_at' => null]);

                throw $exception;
            }

            $chunk = $query->get();
        }
    }


    private function publishRecords(Collection $chunk)
    {
        Log::debug('Publishing OrderProductShipment webhooks for chunk: ' . $chunk->pluck('id')->toJson());

        $records = OrderProductShipmentResource::collection(
            OrderProductShipment::query()
                ->whereIn('id', $chunk->pluck('model_id'))
                ->orderBy('id')
                ->with(['order', 'orderProduct', 'warehouse', 'user', 'product'])
                ->get()
        );

        $payload = collect(['OrderProductShipments' => $records]);

        return SnsService::publishNew(
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
