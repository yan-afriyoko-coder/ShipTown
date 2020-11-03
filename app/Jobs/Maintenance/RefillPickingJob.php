<?php

namespace App\Jobs\Maintenance;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefillPickingJob implements ShouldQueue
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
        \App\Jobs\Maintenance\RefillPaidJob::dispatchNow();
        \App\Jobs\Maintenance\RefillPackingWarehouseJob::dispatchNow();
        \App\Jobs\Maintenance\RefillSingleLineOrdersJob::dispatchNow();

        if (Order::where(['status_code' => 'picking'])->count() > 0) {
            return;
        }

        \App\Jobs\Maintenance\RefillOldOrdersToPickingJob::dispatchNow();
        \App\Jobs\Maintenance\RefillPickingMissingStockJob::dispatchNow();
        \App\Jobs\Maintenance\RefillPickingByOldestJob::dispatchNow();
    }
}
