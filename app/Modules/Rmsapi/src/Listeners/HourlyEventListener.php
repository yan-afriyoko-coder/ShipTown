<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ImportShippingsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;

class HourlyEventListener
{
    /**
     *
     */
    public function handle()
    {
        ProcessImportedProductRecordsJob::dispatch();
        ImportShippingsJob::dispatch();

        RmsapiProductImport::query()
            ->where('when_processed', '<', now()->subMonths(3))
            ->limit(100000);
    }
}
