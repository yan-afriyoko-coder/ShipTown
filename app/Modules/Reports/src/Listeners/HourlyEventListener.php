<?php

namespace App\Modules\Reports\src\Listeners;

use App\Modules\Reports\src\Models\InventoryDashboardReport;

class HourlyEventListener
{
    public function handle()
    {
        $report = new InventoryDashboardReport;

        $report->saveSnapshotToActivity();
    }
}
