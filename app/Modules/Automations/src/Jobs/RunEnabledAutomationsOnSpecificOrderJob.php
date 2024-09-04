<?php

namespace App\Modules\Automations\src\Jobs;

use App\Models\Order;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunEnabledAutomationsOnSpecificOrderJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $order_id;

    public int $uniqueFor = 10;

    public function uniqueId(): string
    {
        return implode('-', [get_class($this), $this->order_id]);
    }

    public function __construct(int $order_id)
    {
        $this->order_id = $order_id;
    }

    public function handle()
    {
        AutomationService::runAutomationsOnOrdersQuery(
            Automation::enabled(),
            Order::placedInLast28DaysOrActive()->where(['id' => $this->order_id])
        );
    }
}
