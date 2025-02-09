<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Modules\MagentoApi\src\Jobs\SyncProductBasePricesJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductSalePricesJob;

class EveryHourEventListener
{
    public function handle(): void
    {
        SyncProductBasePricesJob::dispatch();
        SyncProductSalePricesJob::dispatch();
    }
}
