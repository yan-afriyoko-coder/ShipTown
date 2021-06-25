<?php

namespace App\Observers;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param Order $order
     *
     * @return void
     */
    public function created(Order $order)
    {
        // we will not dispatch CreatedEvent here
        // please use OrderService method
        // CreatedEvent should be dispatched
        // after created OrderProducts etc
    }

    public function saving(Order $order)
    {
        $order->setAttribute('total_quantity_ordered', $order->orderProducts()->sum('quantity_ordered'));
        $order->total_quantity_ordered = $order->orderProducts()->sum('quantity_ordered');
        $order->product_line_count = $order->orderProducts()->count('id');
        $order->is_active = $order->order_status->order_active ?? 1;
        if ($order->isAttributeChanged('is_active')) {
            $order->order_closed_at = $order->is_active ? null : now();
        }
    }

    /**
     * Handle the order "updated" event.
     *
     * @param Order $order
     *
     * @return void
     */
    public function updated(Order $order)
    {
        OrderUpdatedEvent::dispatch($order);
    }
}
