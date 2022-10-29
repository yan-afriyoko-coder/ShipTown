<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\MagentoApi\src\Jobs\SyncCheckFailedProductsJob;

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
        SyncCheckFailedProductsJob::dispatch();
    }
}
