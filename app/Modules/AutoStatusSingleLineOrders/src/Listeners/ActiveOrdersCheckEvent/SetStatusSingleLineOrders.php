<?php

namespace App\Modules\AutoStatusSingleLineOrders\src\Listeners\ActiveOrdersCheckEvent;

use App\Events\Order\ActiveOrderCheckEvent;

/**
 * Class SetPackingWebStatus.
 */
class SetStatusSingleLineOrders
{
    private string $newOrderStatus = 'single_line_orders';

    /**
     * Handle the event.
     *
     * @param ActiveOrderCheckEvent $event
     *
     * @return void
     */
    public function handle(ActiveOrderCheckEvent $event)
    {
        if (($event->order->status_code === 'paid') && ($event->order->product_line_count === 1)) {
            $event->order->log('Single line order detected, changing order status');
            $event->order->update(['status_code' => $this->newOrderStatus]);
        }
    }
}
