<?php

namespace App\Modules\Magento2MSI\src\Listeners;

use App\Modules\Magento2MSI\src\Jobs\AssignInventorySourceJob;
use App\Modules\Magento2MSI\src\Jobs\CheckIfSyncIsRequiredJob;
use App\Modules\Magento2MSI\src\Jobs\FetchStockItemsJob;
use App\Modules\Magento2MSI\src\Jobs\GetProductIdsJob;
use App\Modules\Magento2MSI\src\Jobs\SyncProductInventoryJob;

class EveryFiveMinutesEventListener
{
    public function handle(): void
    {
        GetProductIdsJob::dispatch();
        AssignInventorySourceJob::dispatch();
        FetchStockItemsJob::dispatch();
        CheckIfSyncIsRequiredJob::dispatch();
        SyncProductInventoryJob::dispatch();
    }
}
