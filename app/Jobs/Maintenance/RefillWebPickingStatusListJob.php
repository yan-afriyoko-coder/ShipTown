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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentPickingCount = Order::whereStatusCode('picking')->count();

        $maxCountAllowed = 100;

        if ($maxCountAllowed < $currentPickingCount) {
            return;
        }

        $ordersToPick = Order::whereStatusCode('paid')
            ->limit($maxCountAllowed - $currentPickingCount)
            ->get();

        foreach ($ordersToPick as $order) {
            $order->update(['status_code' => 'picking']);
            info('RefillWebPickingStatusListJob: updated status to picking', ['order_number' => $order->order_number]);
        }
    }
}
