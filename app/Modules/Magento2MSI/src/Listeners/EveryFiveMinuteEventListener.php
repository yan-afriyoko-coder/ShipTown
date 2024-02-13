<?php

namespace App\Modules\Magento2MSI\src\Listeners;

use App\Modules\Magento2MSI\src\Jobs\CheckIfSyncIsRequiredJob;
use App\Modules\Magento2MSI\src\Jobs\FetchStockItemsJob;
use App\Modules\Magento2MSI\src\Jobs\SyncProductInventoryJob;

class EveryFiveMinuteEventListener
{
    public function handle()
    {
        FetchStockItemsJob::dispatch();
        CheckIfSyncIsRequiredJob::dispatch();
        SyncProductInventoryJob::dispatch();
    }
}
