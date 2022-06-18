<?php

namespace App\Modules\Automations\src\Jobs;

use App\Models\CacheLock;
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
 */
class RunAutomationOnOrderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    private int $automation_id;
    private int $order_id;

    public function __construct(int $automation_id, int $order_id)
    {
        $this->automation_id = $automation_id;
        $this->order_id = $order_id;
    }

    public function handle()
    {
        if (! CacheLock::acquire(self::class, $this->automation_id)) {
            return;
        }

        try {
            AutomationService::runAutomationsOnOrdersQuery(
                Automation::whereId($this->automation_id),
                Order::whereId($this->order_id)
            );
        } finally {
            CacheLock::release(self::class, $this->automation_id);
        }
    }
}
