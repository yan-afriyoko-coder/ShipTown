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
        $events->listen('eloquent.created: App\Models\Order','App\Listeners\PublishSnsNotifications@orderCreated');
        $events->listen('eloquent.updated: App\Models\Order','App\Listeners\PublishSnsNotifications@orderUpdated');

        //products
        $events->listen('eloquent.created: App\Models\Product','App\Listeners\PublishSnsNotifications@productCreated');
        $events->listen('eloquent.updated: App\Models\Product','App\Listeners\PublishSnsNotifications@productUpdated');
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
     * @param string $topicName
     */
    private function publishMessageArray(array $data, string $topicName): void
    {
        Log::debug("Publishing SNS message", [
            "topic" => $topicName,
            "data" =>$data
        ]);

        $snsTopic = new SnsController($topicName);

        $snsTopic->publish(json_encode($data));
    }

}
