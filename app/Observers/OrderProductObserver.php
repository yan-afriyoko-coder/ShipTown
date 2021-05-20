<?php

namespace App\Observers;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;
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
        $orderProduct->order->recalculateTotals()->save();

        OrderProductCreatedEvent::dispatch($orderProduct);
    }

    /**
     * Handle the order product "updated" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function updated(OrderProduct $orderProduct)
    {
        $orderProduct->order->recalculateTotals()->save();
        $this->setOrdersPickedAtIfAllPicked($orderProduct);

        OrderProductUpdatedEvent::dispatch($orderProduct);
    }

    /**
     * Handle the order product "deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function deleted(OrderProduct $orderProduct)
    {
        $orderProduct->order->recalculateTotals()->save();
    }

    /**
     * Handle the order product "restored" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function restored(OrderProduct $orderProduct)
    {
        $orderProduct->order->recalculateTotals()->save();
    }

    /**
     * Handle the order product "force deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function forceDeleted(OrderProduct $orderProduct)
    {
        $orderProduct->order->recalculateTotals()->save();
    }

    /**
     * @param OrderProduct $orderProduct
     */
    private function setOrdersPickedAtIfAllPicked(OrderProduct $orderProduct): void
    {
        $orderHasMoreToPick = OrderProduct::query()
            ->where('quantity_to_pick', '>', 0)
            ->where(['order_id' => $orderProduct->order_id])
            ->exists();

        if ($orderHasMoreToPick === false) {
            $orderProduct->order()->update(['picked_at' => now()]);
        }
    }
}
