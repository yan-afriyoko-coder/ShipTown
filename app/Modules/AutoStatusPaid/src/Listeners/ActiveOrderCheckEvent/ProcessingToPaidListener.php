<?php

namespace App\Modules\AutoStatusPaid\src\Listeners\ActiveOrderCheckEvent;

use App\Events\Order\ActiveOrderCheckEvent;
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
     * @param ActiveOrderCheckEvent $event
     *
     * @return void
     */
    public function handle(ActiveOrderCheckEvent $event)
    {
        $order = $event->getOrder();

        SetPaidStatusJob::dispatchNow($order);
    }
}
