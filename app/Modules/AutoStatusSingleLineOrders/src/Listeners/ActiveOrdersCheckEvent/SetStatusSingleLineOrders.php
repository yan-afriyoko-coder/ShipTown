<?php

namespace App\Modules\AutoStatusSingleLineOrders\src\Listeners\ActiveOrdersCheckEvent;

use App\Events\Order\ActiveOrderCheckEvent;

/**
 * Class SetPackingWebStatus
 * @package App\Listeners\Order
 */
class SetStatusSingleLineOrders
{
    /**
     * Handle the event.
     *
     * @param ActiveOrderCheckEvent $event
     * @return void
     */
    public function handle(ActiveOrderCheckEvent $event)
    {
        $newOrderStatus = 'single_line_orders';

        $order = $event->getOrder();

        if ($order->status_code !== 'paid') {
            return;
        }

        if ($order->product_line_count === 1) {
            $order->log("Single line order detected, changing order status");
            $order->update(['status_code' => $newOrderStatus]);
        }
    }
}
