<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RemoveOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RecalculateStatisticsTableJob;

class SyncRequestedEventListener
{
    public function handle(SyncRequestedEvent $event)
    {
        RecalculateStatisticsTableJob::dispatch();
        RemoveOutdatedSalesJob::dispatch();
    }
}
