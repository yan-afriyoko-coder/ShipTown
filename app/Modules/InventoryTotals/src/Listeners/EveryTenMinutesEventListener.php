<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\EnsureInventoryTotalsRecordsExistJob;
use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryTotalsTableJob;

class EveryTenMinutesEventListener
{
    public function handle(): void
    {
        EnsureInventoryTotalsRecordsExistJob::dispatch();
        RecalculateInventoryTotalsTableJob::dispatch();
    }
}
