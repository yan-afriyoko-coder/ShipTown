<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Modules\InventoryMovementsStatistics\src\Jobs\RemoveOutdatedSalesJob;

class Every10MinuteEventListener
{
    public function handle()
    {
        RemoveOutdatedSalesJob::dispatch();
    }
}
