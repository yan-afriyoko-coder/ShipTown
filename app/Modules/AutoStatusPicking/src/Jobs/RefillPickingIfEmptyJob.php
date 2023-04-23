<?php

namespace App\Modules\AutoStatusPicking\src\Jobs;

use App\Models\AutoStatusPickingConfiguration;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefillPickingIfEmptyJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $maxAllowed;

    public function __construct()
    {
        $this->maxAllowed = AutoStatusPickingConfiguration::query()->firstOrCreate([], [])->max_batch_size;
    }

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

        do {
            $countBefore = Order::query()->whereIn('status_code', ['picking'])->count();

            RefillOldOrdersToPickingJob::dispatchNow();
            RefillPickingMissingStockJob::dispatchNow();
            RefillPickingByOldestJob::dispatchNow();

            $countAfter = Order::query()->whereIn('status_code', ['picking'])->count();
        } while (($countAfter < $this->maxAllowed) and ($countBefore !== $countAfter));
    }
}
