<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryRecordsJob;
use App\Modules\InventoryTotals\src\Jobs\UpdateTotalsByWarehouseTagTableJob;

class EveryMinuteEventListener
{
    public function handle()
    {
        RecalculateInventoryRecordsJob::dispatch();
        UpdateTotalsByWarehouseTagTableJob::dispatch();
    }
}
