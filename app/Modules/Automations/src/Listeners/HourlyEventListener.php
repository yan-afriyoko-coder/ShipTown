<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\Automations\src\Jobs\RunEnabledAutomationsJob;

class HourlyEventListener
{
    /**
     * @param HourlyEvent $hourlyEvent
     */
    public function handle(HourlyEvent $hourlyEvent)
    {
        RunEnabledAutomationsJob::dispatch();
    }
}
