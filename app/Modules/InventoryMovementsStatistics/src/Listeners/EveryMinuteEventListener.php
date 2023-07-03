<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Modules\InventoryMovementsStatistics\src\Jobs\AccountForNewSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove14DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove28DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove7DaysOutdatedSalesJob;

class EveryMinuteEventListener
{
    public function handle()
    {
        AccountForNewSalesJob::dispatch();

        Remove7DaysOutdatedSalesJob::dispatch();
        Remove14DaysOutdatedSalesJob::dispatch();
        Remove28DaysOutdatedSalesJob::dispatch();
    }
}
