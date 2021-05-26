<?php

namespace App\Modules\Maintenance\src\Listeners\DailyEvent;

use App\Events\DailyEvent;
use App\Modules\Maintenance\src\Jobs\Products\FixQuantityAvailableJob;
use App\Modules\Maintenance\src\Jobs\Tags\AddMissingOutOfStockTagsJob;
use App\Modules\Maintenance\src\Jobs\Tags\RemoveWrongOutOfStockTagsJob;

class RunDailyMaintenanceJobsListener
{
    /**
     * Handle the event.
     *
     * @param DailyEvent $event
     * @return void
     */
    public function handle(DailyEvent $event)
    {
        FixQuantityAvailableJob::dispatch();
        RemoveWrongOutOfStockTagsJob::dispatch();
        AddMissingOutOfStockTagsJob::dispatch();
    }
}
