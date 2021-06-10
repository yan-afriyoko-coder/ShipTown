<?php

namespace App\Modules\AutoStatusSingleLineOrders\src\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RefillSingleLineOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = Order::where('status_code', 'paid')
            ->where('product_line_count', 1)
            ->get();

        $orders->each(function (Order $order) {
            $order->update([
                'status_code' => 'single_line_orders'
            ]);
        });

        $this->queueData(['orders_changed' => $orders->count()]);
    }
}
