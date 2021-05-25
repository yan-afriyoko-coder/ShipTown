<?php

namespace App\Modules\StatusAutoPilot\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\OrderStatus;

/**
 * Class UpdateClosedAt
 * @package App\Listeners\Order
 */
class UpdateClosedAt
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

        if ($order->isAttributeNotChanged('status_code')) {
            return;
        }

        $closesOrder =  ! $order->order_status->order_active;

        if ($closesOrder and ($order->order_closed_at === null)) {
            $order->log('status "'. $order->status_code .'" closing order ')
                ->update(['order_closed_at' => now()]);
        }
    }
}
