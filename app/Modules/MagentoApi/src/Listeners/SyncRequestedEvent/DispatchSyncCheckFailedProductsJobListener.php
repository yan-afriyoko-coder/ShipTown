<?php

namespace App\Modules\MagentoApi\src\Listeners\SyncRequestedEvent;

use App\Events\SyncRequestedEvent;
use App\Modules\MagentoApi\src\Jobs\EnsureProductRecordsExistJob;
use App\Modules\MagentoApi\src\Jobs\SyncCheckFailedProductsJob;

class DispatchSyncCheckFailedProductsJobListener
{
    /**
     * Handle the event.
     *
     * @param SyncRequestedEvent $event
     *
     * @return void
     */
    public function handle(SyncRequestedEvent $event)
    {
        EnsureProductRecordsExistJob::dispatch();

        SyncCheckFailedProductsJob::dispatch();
    }
}
