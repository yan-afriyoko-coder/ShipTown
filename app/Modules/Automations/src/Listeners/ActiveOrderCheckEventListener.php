<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\Automations\src\Services\AutomationService;

class ActiveOrderCheckEventListener
{
    /**
     * @param ActiveOrderCheckEvent $event
     */
    public function handle(ActiveOrderCheckEvent $event)
    {
        AutomationService::dispatchAutomationsOn($event->order);
    }
}
