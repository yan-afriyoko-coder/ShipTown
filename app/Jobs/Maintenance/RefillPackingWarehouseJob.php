<?php

namespace App\Jobs\Maintenance;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RefillPackingWarehouseJob implements ShouldQueue
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
        Order::where('status_code', 'paid')
            ->get()->each(function ($order) {
                if (OrderService::canFulfill($order, 99)) {
                    $this->updateStatusWithLog(
                        $order,
                        'packing_warehouse',
                        'Can fulfill from warehouse 99'
                    );
                }
            });
    }
}
