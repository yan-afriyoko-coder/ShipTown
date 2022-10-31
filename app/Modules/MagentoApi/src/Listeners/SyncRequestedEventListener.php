<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\MagentoApi\src\Jobs\EnsureProductRecordsExistJob;
use App\Modules\MagentoApi\src\Jobs\FetchBasePricesJob;
use App\Modules\MagentoApi\src\Jobs\FetchStockItemsJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductInventoryJob;

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

        FetchStockItemsJob::dispatch();
        FetchBasePricesJob::dispatch();

        SyncProductInventoryJob::dispatch();
    }
}
