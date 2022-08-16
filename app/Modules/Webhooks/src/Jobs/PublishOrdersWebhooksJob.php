<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Modules\Webhooks\src\Models\PendingWebhook;
use App\Modules\Webhooks\src\Services\SnsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishOrdersWebhooksJob implements ShouldQueue
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
        $query = PendingWebhook::query()
            ->where([
                'model_class' => Order::class,
                'reserved_at' => null,
                'published_at' => null,
            ])
            ->orderBy('id')
            ->limit(10);

        $chunk = $query->get();

        while ($chunk->isNotEmpty()) {
            $pendingWebhookIds = $chunk->pluck('id');

            try {
                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update(['reserved_at' => now()]);

                $this->publishOrderMessage($chunk);

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
     * @throws Exception
     */
    private function publishOrderMessage(Collection $chunk)
    {
        $ordersCollection = OrderResource::collection(
            Order::query()
                ->whereIn('id', $chunk->pluck('model_id'))
                ->orderBy('id')
                ->with([
                    'shippingAddress',
                    'orderShipments',
                    'orderProducts',
                    'orderComments',
                    'orderProductsTotals',
                    'tags',
                ])
                ->get()
        );

        $payload = collect(['Orders' => $ordersCollection]);

        try {
            SnsService::publishNew($payload->toJson());
        } catch (Exception $exception) {
            Log::error('Exception occurred when publishing message', [
                'exception' => $exception->getMessage(),
                'sns_message' => $payload->toJson()
            ]);
            throw $exception;
        }
    }
}
