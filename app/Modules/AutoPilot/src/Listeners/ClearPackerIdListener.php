<?php

namespace App\Modules\AutoPilot\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\AutoPilot\src\Jobs\ClearPackerIdJob;

class ClearPackerIdListener
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     *
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        ClearPackerIdJob::dispatch();
    }
}
