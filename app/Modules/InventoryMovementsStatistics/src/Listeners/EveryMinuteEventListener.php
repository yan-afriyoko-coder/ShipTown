<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Modules\InventoryMovementsStatistics\src\Jobs\AccountForNewSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove14DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove28DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove7DaysOutdatedSalesJob;
use Illuminate\Support\Facades\Bus;

class EveryMinuteEventListener
{
    public function handle()
    {
        AccountForNewSalesJob::dispatch();

        Bus::chain([
            new Remove7DaysOutdatedSalesJob(),
            new Remove14DaysOutdatedSalesJob(),
            new Remove28DaysOutdatedSalesJob(),
        ])->dispatch();
    }
}
