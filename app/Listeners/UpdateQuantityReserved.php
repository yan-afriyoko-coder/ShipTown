<?php

namespace App\Listeners;

use App\Events\EventTypes;
use App\Managers\ProductManager;
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

        foreach ($order->order_json['products'] as $product) {

            ProductManager::reserve(
                $product["sku"],
                $product['quantity'],
                "Order ".$order['order_number']
            );

        }

    }


    public function on_order_deleted(EventTypes $event) {

        $order = $event->data;

        foreach ($order->order_json['products'] as $product) {

            $negativeQuantity = -1 * $product['quantity'];

            ProductManager::reserve(
                $product["sku"],
                 $negativeQuantity,
                "Order ".$order['order_number']
            );

        }

    }
}
