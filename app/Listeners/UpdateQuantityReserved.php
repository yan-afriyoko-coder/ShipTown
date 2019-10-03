<?php

namespace App\Listeners;

use App\Events\EventTypes;
use App\Models\Product;

class UpdateQuantityReserved
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(EventTypes::ORDER_CREATED, 'App\Listeners\UpdateQuantityReserved@on_order_created');
        $events->listen(EventTypes::ORDER_DELETED, 'App\Listeners\UpdateQuantityReserved@on_order_deleted');
    }


    public function on_order_created(EventTypes $event) {

        $order = $event->data;

        $products = $order->order_json['products'];

        foreach ($products as $product) {

            $message = "Order ".$order['order_number'];

            $aProduct = Product::firstOrCreate(["sku" => $product["sku"]]);

            $aProduct->reserve($product['quantity'], $message);

        }

    }


    public function on_order_deleted(EventTypes $event) {

        $order = $event->data;

        foreach ($order->order_json['products'] as $product) {

            $message = "Order ".$order['order_number'];

            $aProduct = Product::firstOrCreate(["sku" => $product["sku"]]);

            $aProduct->reserve( -1 * $product['quantity'], $message);

        }

    }
}
