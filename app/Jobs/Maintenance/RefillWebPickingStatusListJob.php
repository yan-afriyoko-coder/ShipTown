<?php

namespace App\Jobs\Maintenance;

use App\Models\Configuration;
use App\Models\Order;
use App\Services\AutoPilot;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RefillWebPickingStatusListJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $maxDailyAllowed;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxDailyAllowed = AutoPilot::getAutoPilotPackingDailyMax();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentOrdersInProcessCount = Order::whereIn('status_code', ['picking', 'packing_web'])->count();

        logger('Refilling "picking" status', [
            'max_daily_allowed' => $this->maxDailyAllowed,
            'currently_in_process' => $currentOrdersInProcessCount,
        ]);

        if ($this->maxDailyAllowed < $currentOrdersInProcessCount) {
            return;
        }

        $ordersToPick = Order::whereStatusCode('paid')
            ->orderBy('created_at')
            ->limit($this->maxDailyAllowed - $currentOrdersInProcessCount)
            ->get();

        foreach ($ordersToPick as $order) {
            $order->update(['status_code' => 'picking']);
            info('RefillWebPickingStatusListJob: updated status to picking', ['order_number' => $order->order_number]);
        }
    }
}
