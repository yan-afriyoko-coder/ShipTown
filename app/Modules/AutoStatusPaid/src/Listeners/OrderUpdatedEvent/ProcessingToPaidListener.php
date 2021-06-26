<?php

namespace App\Modules\AutoStatusPaid\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoStatusPaid\src\Jobs\SetPaidStatusJob;

/**
 * Class SetPackingWebStatus.
 */
class ProcessingToPaidListener
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

        SetPaidStatusJob::dispatchNow($order);
    }
}
