<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\Api2cart\src\Jobs\DetachNotSyncedTagIfNotAvailableOnlineJob;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Api2cart\src\Jobs\ProcessImportedOrdersJob;
use App\Modules\Api2cart\src\Jobs\RemoveProductLinksIfNotAvailableOnlineJob;
use App\Modules\Api2cart\src\Jobs\SyncProductsJob;
use App\Modules\Api2cart\src\Jobs\SyncVariantsJob;
use App\Modules\Api2cart\src\Jobs\UpdateMissingTypeAndIdJob;
use App\Modules\Api2cart\src\Jobs\VerifyIfProductsInSyncJob;

class HourlyEventListener
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
        SyncProductsJob::dispatch();
        SyncVariantsJob::dispatch();

        RemoveProductLinksIfNotAvailableOnlineJob::dispatch();
        DetachNotSyncedTagIfNotAvailableOnlineJob::dispatch();
    }
}
