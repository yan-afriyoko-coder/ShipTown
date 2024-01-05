<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Modules\InventoryMovementsStatistics\src\Jobs\RepopulateStatisticsTableJob;

class EveryDayEventListener
{
    public function handle()
    {
        RepopulateStatisticsTableJob::dispatch();
    }
}
