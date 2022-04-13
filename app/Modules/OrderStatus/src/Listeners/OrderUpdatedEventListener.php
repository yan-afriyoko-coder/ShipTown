<?php

namespace App\Modules\OrderStatus\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;

class OrderUpdatedEventListener
{
    public function handle(OrderUpdatedEvent $event)
    {
        if ($event->order->is_active != $event->order->orderStatus->order_active) {
            $event->order->is_active = $event->order->orderStatus->order_active;
            $event->order->save();
        }

        if ($event->order->is_on_hold != $event->order->orderStatus->order_on_hold) {
            $event->order->is_on_hold = $event->order->orderStatus->order_on_hold;
            $event->order->save();
        }
    }
}
