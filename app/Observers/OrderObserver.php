<?php

namespace App\Observers;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use Carbon\Carbon;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param Order $order
     * @return void
     */
    public function created(Order $order)
    {
        // we will not dispatch CreatedEvent here
        // please use OrderService method
        // CreatedEvent should be dispatched
        // after created OrderProducts etc
    }

    /**
     * @param Order $order
     */
    public function saved(Order $order)
    {
        // if no more orderProducts to pick exists
        if (!$order->orderProducts()->where('quantity_to_pick', '>', 0)->exists()) {
            if ($order->isNotStatusCode('picking')) {
                return;
            }

            // change status to packing_web
            $order->update([
                'status_code' => 'packing_web',
                'picked_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        OrderUpdatedEvent::dispatch($order);
    }
}
