<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Modules\Webhooks\src\AwsSns;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

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
     */
    public function handle()
    {
        $awaiting_publish_tag = config('webhooks.tags.awaiting.name');

        $products = Product::withAllTags($awaiting_publish_tag)
            ->get();

        $this->queueData(['products_count' => $products->count()]);

        $products->each(function (Product $product) {
            $product->attachTag(config('webhooks.tags.publishing.name'));
            $product->detachTag(config('webhooks.tags.awaiting.name'));

            $productResource = new ProductResource($product);
            if (!AwsSns::publish('products_events', $productResource->toJson())) {
                $product->attachTag(config('webhooks.tags.awaiting.name'));
            }

            $product->detachTag(config('webhooks.tags.publishing.name'));
        });
    }
}
