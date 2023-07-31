<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Modules\InventoryMovementsStatistics\src\Jobs\RemoveOutdatedSalesJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        RemoveOutdatedSalesJob::dispatch();
    }
}
