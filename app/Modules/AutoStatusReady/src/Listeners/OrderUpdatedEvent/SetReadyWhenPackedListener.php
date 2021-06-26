<?php

namespace App\Modules\AutoStatusReady\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoStatusReady\src\Jobs\SetReadyStatusWhenPackedJob;

/**
 * Class SetPackingWebStatus.
 */
class SetReadyWhenPackedListener
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
        $order = $event->getOrder();

        SetReadyStatusWhenPackedJob::dispatchNow($order);
    }
}
