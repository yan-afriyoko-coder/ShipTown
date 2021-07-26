<?php

namespace App\Modules\AutoStatusAwaitingPayment\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoStatusAwaitingPayment\src\Jobs\SetAwaitingPaymentStatusJob;

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
        SetAwaitingPaymentStatusJob::dispatchNow($event->order);
    }
}
