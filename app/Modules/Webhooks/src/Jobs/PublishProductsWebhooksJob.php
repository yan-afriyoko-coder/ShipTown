<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Modules\Webhooks\src\AwsSns;
use App\Modules\Webhooks\src\Models\PendingWebhook;
use App\Modules\Webhooks\src\Services\SnsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Spatie\Activitylog\Models\Activity;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishProductsWebhooksJob implements ShouldQueue
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
                'model_class' => Product::class,
                'reserved_at' => null,
                'published_at' => null,
            ])
            ->orderBy('id')
            ->limit(100);

        $chunk = $query->get();

        while ($chunk->isNotEmpty()) {
            $pendingWebhookIds = $chunk->pluck('id');

            try {
                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update(['reserved_at' => now()]);

                $this->publishProductsMessage($chunk);

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

    private function publishProductsMessage(Collection $chunk)
    {
        $productsCollection = ProductResource::collection(
            Product::query()
                ->whereIn('id', $chunk->pluck('model_id'))
                ->orderBy('id')
                ->with([
                    'aliases',
                    'tags',
                ])
                ->get()
        );

        $payload = collect(['Products' => $productsCollection]);

        SnsService::publishNew(
            $payload->toJson(),
            [
                "warehouse_code" => [
                    "DataType" => "String",
                    "StringValue" => '*'
                ]
            ]
        );
    }
}
