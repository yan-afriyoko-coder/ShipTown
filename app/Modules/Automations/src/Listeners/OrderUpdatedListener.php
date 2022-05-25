<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\Automations\src\Services\AutomationService;

class OrderUpdatedListener
{
    /**
     * @param OrderUpdatedEvent $event
     */
    public function handle(OrderUpdatedEvent $event)
    {
        AutomationService::dispatchAutomationsOn($event->order);
    }
}
