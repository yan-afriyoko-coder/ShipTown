<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\CleanupProductsImportTableJob;

class HourlyEventListener
{
    public function handle()
    {
        CleanupProductsImportTableJob::dispatch();
    }
}
