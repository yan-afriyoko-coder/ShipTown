<?php

namespace App\Modules\InventoryQuantityIncoming\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\InventoryQuantityIncoming\src\Jobs\FixIncorrectQuantityIncomingJob;
use App\Modules\InventoryQuantityIncoming\src\Jobs\FixShouldBe0Job;

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
        FixIncorrectQuantityIncomingJob::dispatch();
        FixShouldBe0Job::dispatch();
    }
}
