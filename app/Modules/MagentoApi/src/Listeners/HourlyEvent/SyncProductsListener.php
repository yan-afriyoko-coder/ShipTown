<?php

namespace App\Modules\MagentoApi\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Modules\MagentoApi\src\Jobs\SyncCheckFailedProductsJob;

class SyncProductsListener
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        if (config('modules.magentoApi.enabled') === false) {
            return;
        }

        SyncCheckFailedProductsJob::dispatch();
    }
}
