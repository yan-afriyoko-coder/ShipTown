<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;

class HourlyEventListener
{
    /**
     *
     */
    public function handle()
    {
        ProcessImportedProductRecordsJob::dispatch();
    }
}
