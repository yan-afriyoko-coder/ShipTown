<?php

namespace App\Modules\Magento2MSI\src\Listeners;

use App\Modules\Magento2MSI\src\Jobs\RecheckIfProductsExistInMagentoJob;

class EveryDayEventListener
{
    public function handle(): void
    {
        RecheckIfProductsExistInMagentoJob::dispatch();
    }
}
