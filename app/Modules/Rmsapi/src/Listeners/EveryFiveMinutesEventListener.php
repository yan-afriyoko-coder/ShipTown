<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ImportAllJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Jobs\UpdateImportedSalesRecordsJob;

class EveryFiveMinutesEventListener
{
    public function handle()
    {
        ImportAllJob::dispatch();

        UpdateImportedSalesRecordsJob::dispatch();
        ProcessImportedSalesRecordsJob::dispatch();
        ProcessImportedProductRecordsJob::dispatch();
    }
}
