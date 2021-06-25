<?php

namespace App\Modules\AutoStatusPicking\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Models\Order;
use App\Modules\AutoStatusPicking\src\Jobs\RefillOldOrdersToPickingJob;
use App\Modules\AutoStatusPicking\src\Jobs\RefillPickingByOldestJob;
use App\Modules\AutoStatusPicking\src\Jobs\RefillPickingMissingStockJob;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class SetPackingWebStatus.
 */
class RefillStatusPickingListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     *
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        if (Order::where(['status_code' => 'picking'])->count() > 0) {
            return;
        }

        RefillOldOrdersToPickingJob::dispatchNow();
        RefillPickingMissingStockJob::dispatchNow();
        RefillPickingByOldestJob::dispatchNow();
    }
}
