<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ImportAllJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Jobs\UpdateImportedSalesRecordsJob;
use Illuminate\Support\Facades\Bus;

class EveryFiveMinutesEventListener
{
    public function handle()
    {
        ImportAllJob::dispatch();
        Bus::chain([
            new UpdateImportedSalesRecordsJob(),
            new ProcessImportedProductRecordsJob(),
            new ProcessImportedSalesRecordsJob(),
        ])->dispatch();
    }
}
