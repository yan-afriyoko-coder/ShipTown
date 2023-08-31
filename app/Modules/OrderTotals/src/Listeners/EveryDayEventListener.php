<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Modules\OrderTotals\src\Services\OrderTotalsService;

class EveryDayEventListener
{
    public function handle()
    {
        OrderTotalsService::dispatchDailyJobs();
    }
}
