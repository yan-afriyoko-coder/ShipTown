<?php

namespace App\Modules\Api2cart\src\Listeners\SyncRequestedEvent;

use App\Events\DailyEvent;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;

class DispatchImportOrdersJobsListener
{
    /**
     * Handle the event.
     *
     * @param DailyEvent $event
     * @return void
     */
    public function handle(DailyEvent $event)
    {
        DispatchImportOrdersJobs::dispatch();
    }
}
