<?php

namespace App\Listeners;

use App\Http\Controllers\SnsController;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Class PublishSnsNotifications
 * @package App\Listeners
 */
class PublishSnsNotifications
{
    /**
     * Register the listeners for SNS publisher
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        //products
        $events->listen('eloquent.created: App\Models\Product', 'App\Listeners\PublishSnsNotifications@productCreated');
        $events->listen('eloquent.updated: App\Models\Product', 'App\Listeners\PublishSnsNotifications@productUpdated');
    }

    /**
     * @param Product $product
     */
    public function productCreated(Product $product)
    {
        $this->publishMessageArray($product->toArray(), 'products_events');
    }

    /**
     * @param Product $product
     */
    public function productUpdated(Product $product)
    {
        $this->publishMessageArray($product->toArray(), 'products_events');
    }

    /**
     * @param array $data
     * @param string $topicName
     */
    private function publishMessageArray(array $data, string $topicName): void
    {
        $snsTopic = new SnsController($topicName);

        $snsTopic->publish(json_encode($data));
    }
}
