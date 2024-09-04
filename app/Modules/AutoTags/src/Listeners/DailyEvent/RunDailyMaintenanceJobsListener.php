<?php

namespace App\Modules\AutoTags\src\Listeners\DailyEvent;

use App\Events\EveryDayEvent;
use App\Modules\AutoTags\src\Jobs\AddMissingOutOfStockTagsJob;
use App\Modules\AutoTags\src\Jobs\AddMissingOversoldTagsJob;
use App\Modules\AutoTags\src\Jobs\RemoveWrongOutOfStockTagsJob;
use App\Modules\AutoTags\src\Jobs\RemoveWrongOversoldTagsJob;

class RunDailyMaintenanceJobsListener
{
    /**
     * Handle the event.
     *
     *
     * @return void
     */
    public function handle(EveryDayEvent $event)
    {
        RemoveWrongOutOfStockTagsJob::dispatch();
        AddMissingOutOfStockTagsJob::dispatch();

        RemoveWrongOversoldTagsJob::dispatch();
        AddMissingOversoldTagsJob::dispatch();
    }
}
