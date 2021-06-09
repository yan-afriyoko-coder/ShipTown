<?php

namespace App\Modules\AutoStatusPackingWarehouse\src\Jobs\Refill;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RefillPackingWarehouseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Order::where('status_code', 'paid')
            ->get()
            ->each(function (Order $order) {
                if (OrderService::canFulfill($order, 99)) {
                    $order->update(['status_code' => 'packing_warehouse']);
                }
            });

        info('Refilled packing_warehouse');
    }
}
