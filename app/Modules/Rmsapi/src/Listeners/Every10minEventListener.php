<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;

class Every10minEventListener
{
    public function handle()
    {
        ProcessImportedProductRecordsJob::dispatch();
    }
}
