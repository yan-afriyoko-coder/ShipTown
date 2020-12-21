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
        $this->updateOrderTotals($orderProduct);
    }

    /**
     * Handle the order product "updated" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function updated(OrderProduct $orderProduct)
    {
        $this->updateOrderTotals($orderProduct);

        $this->setOrdersPickedAtIfAllPicked($orderProduct);
    }

    /**
     * Handle the order product "deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function deleted(OrderProduct $orderProduct)
    {
        $orderProduct->order()->decrement('total_quantity_ordered', $orderProduct['quantity_ordered']);
        $orderProduct->order()->decrement('product_line_count');
    }

    /**
     * Handle the order product "restored" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function restored(OrderProduct $orderProduct)
    {
        $orderProduct->order()->increment('total_quantity_ordered', $orderProduct['quantity_ordered']);
        $orderProduct->order()->increment('product_line_count');
    }

    /**
     * Handle the order product "force deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function forceDeleted(OrderProduct $orderProduct)
    {
        $orderProduct->order()->decrement('total_quantity_ordered', $orderProduct['quantity_ordered']);
        $orderProduct->order()->decrement('product_line_count');
    }

    /**
     * @param OrderProduct $orderProduct
     */
    private function updateOrderTotals(OrderProduct $orderProduct): void
    {
        $order = $orderProduct->order()->first();

        $quantity_to_ship_delta = $orderProduct->quantity_ordered - ($orderProduct->getOriginal('quantity_ordered') ?? 0);

        $order->total_quantity_ordered += $orderProduct->quantity_ordered;
        $order->product_line_count++;
        $order->save();
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
