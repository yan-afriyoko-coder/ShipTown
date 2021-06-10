<?php

namespace App\Modules\AutoStatusPackingWeb\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use Carbon\Carbon;

/**
 * Class SetPackingWebStatus
 * @package App\Listeners\Order
 */
class SetPackingWebStatus
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

        if ($order->status_code !== 'picking') {
            return;
        }

        // if no more orderProducts to pick exists
        if ($order->orderProducts()->where('quantity_to_pick', '>', 0)->exists()) {
            return;
        }

        $order->log('Order fully picked, changing status');
        $order->update([
            'picked_at' => Carbon::now(),
            'status_code' => 'packing_web',
        ]);
    }
}
