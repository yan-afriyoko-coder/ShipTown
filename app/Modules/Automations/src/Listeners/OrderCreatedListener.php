<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Order\OrderCreatedEvent;
use App\Modules\Automations\src\Services\AutomationService;

/**
 *
 */
class OrderCreatedListener
{
    /**
     * @param OrderCreatedEvent $event
     */
    public function handle(OrderCreatedEvent $event)
    {
        AutomationService::runAllAutomations($event);
    }
}
