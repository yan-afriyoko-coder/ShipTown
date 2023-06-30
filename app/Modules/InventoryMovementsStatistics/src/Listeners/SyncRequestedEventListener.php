<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\InventoryMovementsStatistics\src\Jobs\AccountForNewSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove14DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove28DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove7DaysOutdatedSalesJob;

class SyncRequestedEventListener
{
    public function handle(SyncRequestedEvent $event)
    {
        AccountForNewSalesJob::dispatch();

        Remove7DaysOutdatedSalesJob::dispatch();
        Remove14DaysOutdatedSalesJob::dispatch();
        Remove28DaysOutdatedSalesJob::dispatch();
    }
}
