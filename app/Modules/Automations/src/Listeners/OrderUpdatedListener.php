<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\Automations\src\Jobs\RunEnabledAutomationsOnSpecificOrderJob;

class OrderUpdatedListener
{
    /**
     * @param OrderUpdatedEvent $event
     */
    public function handle(OrderUpdatedEvent $event)
    {
        RunEnabledAutomationsOnSpecificOrderJob::dispatchNow($event->order->getKey());
    }
}
