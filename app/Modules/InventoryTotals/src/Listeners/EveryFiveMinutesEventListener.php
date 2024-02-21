<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\UpdateTotalsByWarehouseTagTableJob;

class EveryFiveMinutesEventListener
{
    public function handle()
    {
        UpdateTotalsByWarehouseTagTableJob::dispatch();
    }
}
