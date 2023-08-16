<?php

namespace App\Modules\Maintenance\src\Listeners\DailyEvent;

use App\Events\EveryDayEvent;
use App\Modules\Maintenance\src\Jobs\Products\EnsureAllInventoryRecordsExistsJob;
use App\Modules\Maintenance\src\Jobs\Products\EnsureAllProductPriceRecordsExistsJob;
use App\Modules\Maintenance\src\Jobs\Products\FixQuantityAvailableJob;
use App\Modules\Maintenance\src\Jobs\Products\RecalculateProductQuantityJob;

class RunDailyMaintenanceJobsListener
{
    /**
     * Handle the event.
     *
     * @param EveryDayEvent $event
     *
     * @return void
     */
    public function handle(EveryDayEvent $event)
    {
        EnsureAllInventoryRecordsExistsJob::dispatchAfterResponse();
        EnsureAllProductPriceRecordsExistsJob::dispatchAfterResponse();
        FixQuantityAvailableJob::dispatchAfterResponse();
        RecalculateProductQuantityJob::dispatch();
    }
}
