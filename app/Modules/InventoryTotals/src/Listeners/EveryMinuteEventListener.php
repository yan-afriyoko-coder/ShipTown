<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryRecordsJob;
use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryTotalsByWarehouseTagJob;

class EveryMinuteEventListener
{
    public function handle()
    {
        RecalculateInventoryRecordsJob::dispatch();
        RecalculateInventoryTotalsByWarehouseTagJob::dispatch();
    }
}
