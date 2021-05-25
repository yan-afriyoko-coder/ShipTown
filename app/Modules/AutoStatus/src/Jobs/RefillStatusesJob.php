<?php

namespace App\Modules\AutoStatus\src\Jobs;

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
        Refill\RefillPackingWarehouseJob::dispatchNow();
        Refill\RefillSingleLineOrdersJob::dispatchNow();

        if (Order::where(['status_code' => 'picking'])->count() > 0) {
            return;
        }

        Refill\RefillOldOrdersToPickingJob::dispatchNow();
        Refill\RefillPickingMissingStockJob::dispatchNow();
        Refill\RefillPickingByOldestJob::dispatchNow();

        info('RefillStatusesJob finished');
    }
}
