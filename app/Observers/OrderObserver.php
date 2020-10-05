<?php

namespace App\Observers;

use App\Events\Order\StatusChangedEvent;
use App\Events\Order\UpdatedEvent;
use App\Models\Order;
use App\Models\OrderStatus;

class OrderObserver
{
    public function saving(Order $order)
    {
//        if (OrderStatus::isComplete($order->status_code) && ($order->order_closed_at === null)) {
//            $order->order_closed_at = now();
//        }
    }

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
        UpdatedEvent::dispatch($order);

        if ($order['status_code'] !== $order->getOriginal('status_code')) {
            StatusChangedEvent::dispatch($order);
        }
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
