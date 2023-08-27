<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\CleanupImportTablesJob;
use App\Modules\Rmsapi\src\Jobs\ImportAllJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Jobs\UpdateImportedSalesRecordsJob;
use Exception;

class SyncRequestedEventListener
{
    /**
     * @throws Exception
     */
    public function handle()
    {
        CleanupImportTablesJob::dispatch();
        ImportAllJob::dispatch();
        UpdateImportedSalesRecordsJob::dispatch();
        ProcessImportedProductRecordsJob::dispatch();
        ProcessImportedSalesRecordsJob::dispatch();
    }
}
