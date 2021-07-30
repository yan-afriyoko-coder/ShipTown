<?php

namespace App\Modules\AutoStatusReady\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoStatusReady\src\Jobs\SetReadyStatusWhenPackedJob;

/**
 * Class SetPackingWebStatus.
 */
class OrderUpdatedListener
{
    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     *
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        if (($event->order->isAttributeChanged('is_packed')) and ($event->order->is_packed)) {
            $event->order->log('Order fully packed, changing status to "complete"');
            $event->order->update(['status_code' => 'complete']);
        }
    }
}
