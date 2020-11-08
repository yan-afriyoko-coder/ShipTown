<?php

namespace App\Jobs\Refill;

use App\Models\Order;
use App\Services\AutoPilot;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RefillOldOrdersToPickingJob implements ShouldQueue
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
        $this->maxDailyAllowed = AutoPilot::getMaxOrderAgeAllowed();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentOrdersInProcessCount = Order::whereIn('status_code', ['picking'])->count();

        logger('Refilling "picking" status (old orders)', [
            'currently_in_process' => $currentOrdersInProcessCount,
            'max_daily_allowed' => $this->maxDailyAllowed,
        ]);

        $orderRequiredCount = $this->maxDailyAllowed - $currentOrdersInProcessCount;

        if ($orderRequiredCount <= 0) {
            return;
        }

        $orders = Order::whereStatusCode('paid')
            ->where('order_placed_at', '<', Carbon::now()->subDays(AutoPilot::getMaxOrderAgeAllowed()))
            ->orderBy('created_at')
            ->limit($orderRequiredCount)
            ->get();

        foreach ($orders as $order) {
            $order->update(['status_code' => 'picking']);
            info('RefillOldOrdersToPickingJob: updated status to picking', ['order_number' => $order->order_number]);
        }
    }
}
