<?php

namespace App\Modules\AutoStatusPaid\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;

/**
 * Class UpdateClosedAt
 * @package App\Listeners\Order
 */
class ProcessingToPaidListener
{
    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        $order = $event->getOrder();

        if ($order->isStatusCode('processing') and ($order->isPaid)) {
            $order->log('Order paid in full, changing status to "paid"');
            $order->update(['status_code' => 'paid']);
        }
    }
}
