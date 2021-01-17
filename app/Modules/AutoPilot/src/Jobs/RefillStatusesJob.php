<?php

namespace App\Modules\AutoPilot\src\Jobs;

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
        \App\Modules\AutoPilot\src\Jobs\Refill\RefillPackingWarehouseJob::dispatchNow();
        \App\Modules\AutoPilot\src\Jobs\Refill\RefillSingleLineOrdersJob::dispatchNow();

        if (Order::where(['status_code' => 'picking'])->count() > 0) {
            return;
        }

        \App\Modules\AutoPilot\src\Jobs\Refill\RefillOldOrdersToPickingJob::dispatchNow();
        \App\Modules\AutoPilot\src\Jobs\Refill\RefillPickingMissingStockJob::dispatchNow();
        \App\Modules\AutoPilot\src\Jobs\Refill\RefillPickingByOldestJob::dispatchNow();

        info('RefillStatusesJob finished');
    }
}
