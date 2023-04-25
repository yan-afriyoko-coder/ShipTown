<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\CleanupProductsImportTableJob;
use App\Modules\Rmsapi\src\Jobs\ImportProductsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Exception;

class Every10minEventListener
{
    /**
     * @throws Exception
     */
    public function handle()
    {
        CleanupProductsImportTableJob::dispatch();

        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            new ImportProductsJob($rmsapiConnection->id);
        }

        ProcessImportedProductRecordsJob::dispatch();
    }
}
