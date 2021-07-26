<?php

namespace App\Modules\AutoStatusAwaitingPayment\src\Listeners\ActiveOrderCheckEvent;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\AutoStatusAwaitingPayment\src\Jobs\SetAwaitingPaymentStatusJob;

/**
 * Class SetPackingWebStatus.
 */
class ProcessingToPaidListener
{
    /**
     * Handle the event.
     *
     * @param ActiveOrderCheckEvent $event
     *
     * @return void
     */
    public function handle(ActiveOrderCheckEvent $event)
    {
        SetAwaitingPaymentStatusJob::dispatchNow($event->order);
    }
}
