<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Jobs\UpdateImportedSalesRecordsJob;

class EveryMinuteEventListener
{
    public function handle()
    {
        UpdateImportedSalesRecordsJob::dispatch();
        ProcessImportedSalesRecordsJob::dispatch();
    }
}
