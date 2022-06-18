<?php

namespace App\Modules\Automations\src\Jobs;

use App\Models\Order;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * @property int|null order_id
 */
class RunEnabledAutomationsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    public function handle()
    {
        AutomationService::runAutomationsOnOrdersQuery(
            Automation::enabled(),
            Order::placedInLast28DaysOrActive()
        );
    }
}
