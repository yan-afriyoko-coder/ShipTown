<?php

namespace App\Modules\Api2cart\src\Listeners\DailyEvent;

use App\Events\DailyEvent;
use App\Modules\Api2cart\src\Jobs\ResyncCheckFailedTaggedJob;

class RunCheckFailedTaggedJobListener
{
    /**
     * Handle the event.
     *
     * @param DailyEvent $event
     * @return void
     */
    public function handle(DailyEvent $event)
    {
        ResyncCheckFailedTaggedJob::dispatch();
    }
}
