<?php

namespace App\Modules\AutoStatusPicking\src\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RefillPickingIfEmptyJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Order::where(['status_code' => 'picking'])->count() > 0) {
            return;
        }

        RefillOldOrdersToPickingJob::dispatchNow();
        RefillPickingMissingStockJob::dispatchNow();
        RefillPickingByOldestJob::dispatchNow();
    }
}
