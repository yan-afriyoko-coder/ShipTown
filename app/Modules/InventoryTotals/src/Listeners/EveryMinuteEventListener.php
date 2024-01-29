<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryRecordsJob;

class EveryMinuteEventListener
{
    public function handle()
    {
        RecalculateInventoryRecordsJob::dispatch();
    }
}
