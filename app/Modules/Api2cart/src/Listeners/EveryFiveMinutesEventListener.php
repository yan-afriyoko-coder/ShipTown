<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Api2cart\src\Jobs\ProcessImportedOrdersJob;

class EveryFiveMinutesEventListener
{
    public function handle()
    {
        DispatchImportOrdersJobs::dispatch();
        ProcessImportedOrdersJob::dispatch();
    }
}
