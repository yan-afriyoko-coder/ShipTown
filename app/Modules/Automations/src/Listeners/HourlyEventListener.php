<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;
use App\Modules\Automations\src\Services\AutomationService;

class HourlyEventListener
{
    /**
     * @param HourlyEvent $hourlyEvent
     */
    public function handle(HourlyEvent $hourlyEvent)
    {
        RunAutomationsOnActiveOrdersJob::dispatch();
    }
}
