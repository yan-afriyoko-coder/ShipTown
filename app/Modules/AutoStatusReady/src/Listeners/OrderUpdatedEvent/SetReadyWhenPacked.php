<?php

namespace App\Modules\AutoStatusReady\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\OrderStatus;

/**
 * Class UpdateClosedAt
 * @package App\Listeners\Order
 */
class SetReadyWhenPacked
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
        if ($order->is_packed === false) {
            return;
        }

        if ($order->status_code === 'ready') {
            return;
        }

        if ($order->isStatusCodeIn(OrderStatus::getClosedStatuses())) {
            return;
        }

        $order->log('Order fully packed, changing status to "ready"')
            ->update(['status_code' => 'ready']);
    }
}
