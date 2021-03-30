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
     * @return void
     */
    public function created(Order $order)
    {
        // we will not dispatch CreatedEvent here
        // please user OrderService method
        // CreatedEvent should be dispatched
        // after created OrderProducts etc
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        // if all shipped
        if ($order->orderProducts->where('quantity_to_pick', '>', 0)->exists()) {
            // change status to packing_web
            $order->update([
                'status_code' => 'packing_web'
            ]);
        }

        OrderUpdatedEvent::dispatch($order);
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the order "restored" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
