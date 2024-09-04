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

class RunAutomationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $automation_id;

    public function __construct(int $automation_id)
    {
        $this->automation_id = $automation_id;
    }

    public function handle()
    {
        AutomationService::runAutomationsOnOrdersQuery(
            Automation::whereId($this->automation_id),
            Order::placedInLast28DaysOrActive()
        );
    }
}
