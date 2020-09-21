<?php

namespace App\Jobs\Maintenance;

use App\Models\Order;
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
        $this->maxDailyAllowed = 100;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentOrdersInProcessCount = Order::whereIn('status_code', ['picking', 'ready', 'packing_web'])->count();

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
