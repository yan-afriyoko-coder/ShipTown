<?php

namespace App\Modules\Reports\src\Listeners;

use App\Modules\Reports\src\Jobs\DispatchSaveInventoryDashboardReport;

class EveryMinuteEventListener
{
    public function handle(): void
    {
        DispatchSaveInventoryDashboardReport::dispatch();
    }
}
