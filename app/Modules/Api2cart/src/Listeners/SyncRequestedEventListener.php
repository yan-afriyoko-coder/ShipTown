<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Modules\Api2cart\src\Jobs\FetchSimpleProductsInfoJob;
use App\Modules\Api2cart\src\Jobs\FetchVariantsInfoJob;
use App\Modules\Api2cart\src\Jobs\SyncProductsJob;
use App\Modules\Api2cart\src\Jobs\SyncVariantsJob;
use App\Modules\Api2cart\src\Jobs\UpdateMissingTypeAndIdJob;
use App\Modules\Api2cart\src\Jobs\VerifyIfProductsInSyncJob;

class SyncRequestedEventListener
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
        UpdateMissingTypeAndIdJob::dispatch();
        VerifyIfProductsInSyncJob::dispatch();
        FetchSimpleProductsInfoJob::dispatch();
        FetchVariantsInfoJob::dispatch();

        SyncProductsJob::dispatch();
        SyncVariantsJob::dispatch();
    }
}
