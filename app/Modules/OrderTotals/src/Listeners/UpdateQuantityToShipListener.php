<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;

/**
 *
 */
class UpdateQuantityToShipListener
{
    /**
     * @param OrderProductUpdatedEvent | OrderProductCreatedEvent $event
     */
    public function handle($event)
    {
        $order = $event->orderProduct->order;

        $order->total_quantity_ordered = $order->orderProducts()->sum('quantity_ordered');
        $order->total_quantity_to_ship = $order->orderProducts()->sum('quantity_to_ship');
        $order->product_line_count = $order->orderProducts()->count('id');
        $order->save();
    }
}
