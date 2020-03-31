<?php

namespace App\Listeners;

use App\Events\EventTypes;
use App\Managers\ProductManager;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Arr;
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
//        $events->listen('eloquent.created: App\Models\Order', 'App\Listeners\UpdateQuantityReserved@on_order_created');
//        $events->listen('eloquent.updated: App\Models\Order', 'App\Listeners\UpdateQuantityReserved@on_order_updated');
//        $events->listen('eloquent.deleted: App\Models\Order', 'App\Listeners\UpdateQuantityReserved@on_order_deleted');
    }

    public function on_order_updated(Order $order)
    {
        $order_old = json_decode($order->getOriginal()['order_as_json'], true);

        $this->releaseQuantities($order_old);

        $order_new = json_decode($order->getAttributes()['order_as_json'], true);

        $this->reserveQuantities($order_new);
    }

    public function on_order_created(Order $order)
    {
        $order = $order->order_as_json;

        $this->reserveQuantities($order);
    }


    public function on_order_deleted(Order $order)
    {
        $order = $order->order_as_json;

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
        if(!Arr::has($order, 'products')) {
            return;
        }

        foreach ($order['products'] as $product) {

            ProductManager::release(
                $product["sku"],
                $product['quantity'],
                "Order " . $order['order_number']
            );

        }
    }
}
