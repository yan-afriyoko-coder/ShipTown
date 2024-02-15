<?php

namespace App\Modules\Magento2MSI\src\Listeners;

use App\Modules\Magento2MSI\src\Jobs\EnsureProductRecordsExistJob;

class EveryHourEventListener
{
    public function handle()
    {
        EnsureProductRecordsExistJob::dispatch();
    }
}
