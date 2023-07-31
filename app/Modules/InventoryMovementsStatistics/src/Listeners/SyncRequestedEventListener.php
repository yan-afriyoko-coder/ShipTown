<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RemoveOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RepopulateStatisticsTableJob;

class SyncRequestedEventListener
{
    public function handle(SyncRequestedEvent $event)
    {
        RepopulateStatisticsTableJob::dispatch();
        RemoveOutdatedSalesJob::dispatch();
    }
}
