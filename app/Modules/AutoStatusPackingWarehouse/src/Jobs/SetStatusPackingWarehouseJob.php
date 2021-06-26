<?php

namespace App\Modules\AutoStatusPackingWarehouse\src\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetStatusPackingWarehouseJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Order $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->order;

        $sourceLocationId = 99;
        $newOrderStatus = 'packing_warehouse';

        if ($order->status_code !== 'paid') {
            return;
        }

        if (OrderService::canFulfill($order, $sourceLocationId)) {
            $order->log("Possible to fulfill from location $sourceLocationId, changing order status");
            $order->update(['status_code' => $newOrderStatus]);
        }
    }
}
