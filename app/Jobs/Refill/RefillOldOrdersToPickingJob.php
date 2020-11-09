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
    private $currentOrdersInProcessCount;
    private $ordersRequiredCount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxDailyAllowed = AutoPilot::getMaxOrderAgeAllowed();

        $this->currentOrdersInProcessCount = Order::whereIn('status_code', ['picking'])->count();

        $this->ordersRequiredCount = $this->maxDailyAllowed - $this->currentOrdersInProcessCount;

        info('Refilling "picking" status (old orders)', [
            'currently_in_process' => $this->currentOrdersInProcessCount,
            'max_daily_allowed' => $this->maxDailyAllowed,
            'ordersRequiredCount' => $this->ordersRequiredCount,
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->ordersRequiredCount <= 0) {
            return;
        }

        $orders = Order::whereStatusCode('paid')
            ->where('order_placed_at', '<', Carbon::now()->subDays(AutoPilot::getMaxOrderAgeAllowed()))
            ->orderBy('created_at')
            ->limit($this->ordersRequiredCount)
            ->get();

        foreach ($orders as $order) {
            activity()->performedOn($order)
                ->log('Outdated order, status_code => "picking"');
            $order->update(['status_code' => 'picking']);
        }
    }
}
