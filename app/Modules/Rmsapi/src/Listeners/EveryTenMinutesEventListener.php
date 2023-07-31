<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\CleanupProductsImportTableJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        CleanupProductsImportTableJob::dispatch();
    }
}
