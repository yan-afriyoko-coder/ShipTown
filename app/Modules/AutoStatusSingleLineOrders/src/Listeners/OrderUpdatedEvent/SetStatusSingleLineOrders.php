<?php

namespace App\Modules\AutoStatusSingleLineOrders\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;

/**
 * Class SetPackingWebStatus.
 */
class SetStatusSingleLineOrders
{
    private string $newOrderStatus = 'single_line_orders';

    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     *
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        if (($event->order->status_code === 'paid') && ($event->order->product_line_count === 1)) {
            $event->order->log('Single line order detected, changing order status');
            $event->order->update(['status_code' => $this->newOrderStatus]);
        }
    }
}
