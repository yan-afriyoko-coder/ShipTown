<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Modules\MagentoApi\src\Jobs\CheckIfSyncIsRequiredJob;
use App\Modules\MagentoApi\src\Jobs\EnsureProductPriceIdIsFilledJob;
use App\Modules\MagentoApi\src\Jobs\EnsureProductRecordsExistJob;
use App\Modules\MagentoApi\src\Jobs\FetchBasePricesJob;
use App\Modules\MagentoApi\src\Jobs\FetchSpecialPricesJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductBasePricesBulkJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductBasePricesJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductSalePricesBulkJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductSalePricesJob;

class EveryTenMinutesEventListener
{
    public function handle(): void
    {
        EnsureProductRecordsExistJob::dispatch();
        EnsureProductPriceIdIsFilledJob::dispatch();

        FetchBasePricesJob::dispatch();
        FetchSpecialPricesJob::dispatch();

        SyncProductBasePricesBulkJob::dispatch();
        SyncProductSalePricesBulkJob::dispatch();
    }
}
