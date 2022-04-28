<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\DailyEvent;
use App\Modules\Api2cart\src\Jobs\CheckForOutOfSyncPricesJob;
use App\Modules\Api2cart\src\Jobs\PushOutOfSyncPricingJob;
use App\Modules\Api2cart\src\Jobs\ResyncCheckFailedTaggedJob;
use App\Modules\Api2cart\src\Jobs\ResyncLastDayJob;
use App\Modules\Api2cart\src\Jobs\ResyncSyncErrorsTaggedJob;

class DailyEventListener
{
    /**
     * Handle the event.
     *
     * @param DailyEvent $event
     *
     * @return void
     */
    public function handle(DailyEvent $event)
    {
        ResyncCheckFailedTaggedJob::dispatch();
        ResyncLastDayJob::dispatch();
        ResyncSyncErrorsTaggedJob::dispatch();
        PushOutOfSyncPricingJob::dispatch();
        CheckForOutOfSyncPricesJob::dispatch();
    }
}
