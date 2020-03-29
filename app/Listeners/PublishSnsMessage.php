<?php

namespace App\Listeners;

use App\Http\Controllers\SnsTopicController;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Class PublishSnsMessage
 * @package App\Listeners
 */
class PublishSnsMessage
{
    /**
     * Register the listeners for SNS publisher
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen('eloquent.created: App\Models\Order','App\Listeners\PublishSnsMessage@orderCreated');
        $events->listen('eloquent.updated: App\Models\Order','App\Listeners\PublishSnsMessage@orderUpdated');

        //products
        $events->listen('eloquent.created: App\Models\Product','App\Listeners\PublishSnsMessage@productCreated');
        $events->listen('eloquent.updated: App\Models\Product','App\Listeners\PublishSnsMessage@productUpdated');
    }

    /**
     * @param Order $order
     */
    public function orderCreated(Order $order)
    {
        $this->publishMessageArray($order->toArray(), "orders_events");
    }

    /**
     * @param Order $order
     */
    public function orderUpdated(Order $order)
    {
        $this->publishMessageArray($order->toArray(), "orders_events");
    }

    /**
     * @param Product $product
     */
    public function productCreated(Product $product)
    {
        $this->publishMessageArray($product->toArray(),'products_events');
    }

    /**
     * @param Product $product
     */
    public function productUpdated(Product $product)
    {
        $this->publishMessageArray($product->toArray(),'products_events');
    }

    /**
     * @param array $data
     * @param string $topic_prefix
     */
    private function publishMessageArray(array $data, string $topic_prefix): void
    {
        Log::debug("Publishing SNS message ($topic_prefix)", $data);

        $snsTopic = new SnsTopicController($topic_prefix);

        $snsTopic->publish(json_encode($data));
    }

}
