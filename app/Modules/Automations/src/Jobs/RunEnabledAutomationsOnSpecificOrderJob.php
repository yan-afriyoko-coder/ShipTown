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

/**
 *
 */
class RunEnabledAutomationsOnSpecificOrderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $order_id;

    public function __construct(int $order_id)
    {
        $this->order_id = $order_id;
    }

    public function handle()
    {
        if (! CacheLock::acquire(self::class, $this->order_id)) {
            return;
        }

        try {
            AutomationService::runAutomationsOnOrdersQuery(
                Automation::enabled(),
                Order::placedInLast28DaysOrActive()->where(['id' => $this->order_id])
            );
        } finally {
            CacheLock::release(self::class, $this->order_id);
        }
    }
}
