<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\MagentoApi\src\Jobs\EnsureProductRecordsExistJob;
use App\Modules\MagentoApi\src\Jobs\FetchStockItemsJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductInventoryJob;

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
        EnsureProductRecordsExistJob::dispatch();

        FetchStockItemsJob::dispatch();

        SyncProductInventoryJob::dispatch();
    }
}
