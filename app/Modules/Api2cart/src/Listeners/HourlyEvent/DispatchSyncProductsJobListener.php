<?php

namespace App\Modules\Api2cart\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Modules\Api2cart\src\Jobs\SyncProductsJob;

class DispatchSyncProductsJobListener
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
        SyncProductsJob::dispatch();
    }
}
