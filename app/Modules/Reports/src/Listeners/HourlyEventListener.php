<?php

namespace App\Modules\Reports\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\Reports\src\Models\InventoryDashboardReport;

class HourlyEventListener
{
    public function handle(HourlyEvent $event)
    {
        $report = new InventoryDashboardReport();

        $report->saveSnapshotToActivity();
    }
}
