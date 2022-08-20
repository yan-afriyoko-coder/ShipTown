<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\Rmsapi\src\Jobs\DispatchImportJobs;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;

class SyncRequestedEventListener
{
    /**
     * Handle the event.
     *
     * @param SyncRequestedEvent $event
     *
     * @return void
     */
    public function handle(SyncRequestedEvent $event)
    {
        DispatchImportJobs::dispatch();

        ProcessImportedProductRecordsJob::dispatch();
    }
}
