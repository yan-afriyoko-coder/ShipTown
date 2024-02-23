<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\EnsureInventoryTotalsByWarehouseTagRecordsExistJob;
use App\Modules\InventoryTotals\src\Jobs\EnsureInventoryTotalsRecordsExistJob;
use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryTotalsByWarehouseTagJob;
use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryTotalsTableJob;

class SyncRequestedEventListener
{
    public function handle()
    {
        EnsureInventoryTotalsRecordsExistJob::dispatch();
        RecalculateInventoryTotalsTableJob::dispatch();

        EnsureInventoryTotalsByWarehouseTagRecordsExistJob::dispatch();
        RecalculateInventoryTotalsByWarehouseTagJob::dispatch();
    }
}
