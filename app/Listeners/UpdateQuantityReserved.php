<?php

namespace App\Listeners;

use App\Events\EventTypes;
use App\Managers\ProductManager;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

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
        $events->listen(EventTypes::ORDER_UPDATED, 'App\Listeners\UpdateQuantityReserved@on_order_updated');
        $events->listen(EventTypes::ORDER_DELETED, 'App\Listeners\UpdateQuantityReserved@on_order_deleted');
    }

    public function on_order_updated(EventTypes $event)
    {
        $order_old = json_decode($event->data['original']['order_as_json'], true);

        $this->releaseQuantities($order_old);

        $order_new = json_decode($event->data['original']['order_as_json'], true);

        $this->reserveQuantities($order_new);
    }

    public function on_order_created(EventTypes $event)
    {
        $order = $event->data->order_as_json;

        $this->reserveQuantities($order);
    }


    public function on_order_deleted(EventTypes $event)
    {
        $order = $event->data->order_as_json;

        $this->releaseQuantities($order);

    }

    /**
     * @param $order
     */
    private function reserveQuantities($order): void
    {
        foreach ($order['products'] as $product) {

            ProductManager::reserve(
                $product["sku"],
                $product['quantity'],
                "Order " . $order['order_number']
            );

        }
    }

    /**
     * @param $order
     */
    private function releaseQuantities($order): void
    {
        foreach ($order['products'] as $product) {

            ProductManager::reserve(
                $product["sku"],
                $product['quantity'] * (-1),
                "Order " . $order['order_number']
            );

        }
    }
}
