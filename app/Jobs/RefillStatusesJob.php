<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefillStatusesJob implements ShouldQueue
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
        \App\Modules\AutoPilot\src\Jobs\Refill\RefillPaidJob::dispatchNow();
        \App\Jobs\Orders\Refill\RefillPackingWarehouseJob::dispatchNow();
        \App\Jobs\Orders\Refill\RefillSingleLineOrdersJob::dispatchNow();

        if (Order::where(['status_code' => 'picking'])->count() > 0) {
            return;
        }

        \App\Jobs\Orders\Refill\RefillOldOrdersToPickingJob::dispatchNow();
        \App\Jobs\Orders\Refill\RefillPickingMissingStockJob::dispatchNow();
        \App\Jobs\Orders\Refill\RefillPickingByOldestJob::dispatchNow();
    }
}
