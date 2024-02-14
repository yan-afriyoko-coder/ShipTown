<?php

namespace App\Modules\Magento2MSI\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\Magento2MSI\src\Jobs\CheckIfSyncIsRequiredJob;
use App\Modules\Magento2MSI\src\Jobs\EnsureProductRecordsExistJob;
use App\Modules\Magento2MSI\src\Jobs\FetchStockItemsJob;

class SyncRequestedEventListener
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
        CheckIfSyncIsRequiredJob::dispatch();
        FetchStockItemsJob::dispatch();
    }
}
