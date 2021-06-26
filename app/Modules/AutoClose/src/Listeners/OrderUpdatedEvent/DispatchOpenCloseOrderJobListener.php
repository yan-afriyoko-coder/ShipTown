<?php

namespace App\Modules\AutoClose\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoClose\src\Jobs\OpenCloseOrderJob;

/**
 * Class SetPackingWebStatus.
 */
class DispatchOpenCloseOrderJobListener
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
        OpenCloseOrderJob::dispatchNow($event->getOrder());
    }
}
