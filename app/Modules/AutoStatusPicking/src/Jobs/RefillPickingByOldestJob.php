<?php

namespace App\Modules\AutoStatusPicking\src\Jobs;

use App\Models\Order;
use App\Services\AutoPilot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RefillPickingByOldestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    private $maxDailyAllowed;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxDailyAllowed = AutoPilot::getBatchSize();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentOrdersInProcessCount = Order::whereIn('status_code', ['picking'])->count();

        logger('Refilling "picking" status', [
            'max_daily_allowed'    => $this->maxDailyAllowed,
            'currently_in_process' => $currentOrdersInProcessCount,
        ]);

        if ($this->maxDailyAllowed <= $currentOrdersInProcessCount) {
            return;
        }

        Order::whereStatusCode('paid')
            ->orderBy('created_at')
            ->limit($this->maxDailyAllowed - $currentOrdersInProcessCount)
            ->get()
            ->each(function ($order) {
                $order->update(['status_code' => 'picking']);
                info('RefillPickingJob: updated status to picking', ['order_number' => $order->order_number]);

                return true;
            });
    }
}
