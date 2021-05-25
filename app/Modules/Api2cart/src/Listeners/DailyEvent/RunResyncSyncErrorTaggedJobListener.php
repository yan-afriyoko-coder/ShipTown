<?php

namespace App\Modules\Api2cart\src\Listeners\DailyEvent;

use App\Events\DailyEvent;
use App\Modules\Api2cart\src\Jobs\ResyncCheckFailedTaggedJob;
use App\Modules\Api2cart\src\Jobs\ResyncLastDayJob;
use App\Modules\Api2cart\src\Jobs\ResyncSyncErrorsTaggedJob;

class RunResyncSyncErrorTaggedJobListener
{
    /**
     * Handle the event.
     *
     * @param DailyEvent $event
     * @return void
     */
    public function handle(DailyEvent $event)
    {
        ResyncSyncErrorsTaggedJob::dispatch();
    }
}
