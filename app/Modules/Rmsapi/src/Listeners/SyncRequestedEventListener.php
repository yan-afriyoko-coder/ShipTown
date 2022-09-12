<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\DispatchImportJobs;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;

class SyncRequestedEventListener
{
    public function handle()
    {
        DispatchImportJobs::dispatch();

        ProcessImportedProductRecordsJob::dispatch();
    }
}
