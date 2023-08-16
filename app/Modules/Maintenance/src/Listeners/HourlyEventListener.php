<?php

namespace App\Modules\Maintenance\src\Listeners;

use App\Modules\Maintenance\src\Jobs\EnsureProductSkusPresentInAliasesJob;
use App\Modules\Maintenance\src\Jobs\temp\FillPreviousMovementIdJob;
use App\Modules\Maintenance\src\Jobs\temp\StockInReservationWarehouseMonitorJob;

class HourlyEventListener
{
    public function handle()
    {
        StockInReservationWarehouseMonitorJob::dispatch();
        EnsureProductSkusPresentInAliasesJob::dispatch();
        FillPreviousMovementIdJob::dispatch();
    }
}
