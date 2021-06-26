<?php

namespace App\Modules\AutoStatusPackingWeb\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoStatusPackingWeb\src\Jobs\SetPackingWebStatusJob;

/**
 * Class SetPackingWebStatus.
 */
class SetPackingWebStatus
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

        SetPackingWebStatusJob::dispatchNow($order);
    }
}
