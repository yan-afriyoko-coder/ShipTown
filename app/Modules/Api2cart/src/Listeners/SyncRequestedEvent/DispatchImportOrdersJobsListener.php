<?php

namespace App\Modules\Api2cart\src\Listeners\SyncRequestedEvent;

use App\Events\SyncRequestedEvent;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;

class DispatchImportOrdersJobsListener
{
    /**
     * Handle the event.
     *
     * @param SyncRequestedEvent $event
     * @return void
     */
    public function handle(SyncRequestedEvent $event)
    {
        DispatchImportOrdersJobs::dispatch();
    }
}
