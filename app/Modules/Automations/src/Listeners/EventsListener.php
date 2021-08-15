<?php

namespace App\Modules\Automations\src\Listeners;

use App\Modules\Automations\src\Services\AutomationService;

class EventsListener
{
    /**
     * @param $event
     */
    public function handle($event)
    {
        ray($event);
        AutomationService::runAllAutomations($event);
    }
}
