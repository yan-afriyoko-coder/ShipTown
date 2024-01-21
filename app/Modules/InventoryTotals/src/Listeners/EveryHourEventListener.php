<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryRecordsJob;

class EveryHourEventListener
{
    public function handle()
    {
        RecalculateInventoryRecordsJob::dispatch();
    }
}
