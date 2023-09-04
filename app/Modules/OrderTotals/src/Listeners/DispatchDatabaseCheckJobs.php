<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Modules\OrderTotals\src\Services\OrderTotalsService;

class DispatchDatabaseCheckJobs
{
    public function handle()
    {
        OrderTotalsService::dispatchAllJobs();
    }
}
