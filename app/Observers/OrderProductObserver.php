<?php

namespace App\Observers;

use App\Models\OrderProduct;

class OrderProductObserver
{
    /**
     * Handle the order product "created" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function created(OrderProduct $orderProduct)
    {
        $orderProduct->order()->increment('products_count', $orderProduct['quantity']);
    }

    /**
     * Handle the order product "updated" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function updated(OrderProduct $orderProduct)
    {
        $quantity_delta = $orderProduct['quantity'] - $orderProduct->getOriginal('quantity');

        $orderProduct->order()->increment('products_count', $quantity_delta);
    }

    /**
     * Handle the order product "deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function deleted(OrderProduct $orderProduct)
    {
        $orderProduct->order()->decrement('products_count', $orderProduct['quantity']);
    }

    /**
     * Handle the order product "restored" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function restored(OrderProduct $orderProduct)
    {
        $orderProduct->order()->increment('products_count', $orderProduct['quantity']);
    }

    /**
     * Handle the order product "force deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function forceDeleted(OrderProduct $orderProduct)
    {
        $orderProduct->order()->decrement('products_count', $orderProduct['quantity']);
    }
}
