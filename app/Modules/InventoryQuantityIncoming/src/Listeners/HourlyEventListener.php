<?php

namespace App\Modules\InventoryQuantityIncoming\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\InventoryQuantityIncoming\src\Jobs\FixIncorrectQuantityIncomingJob;

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
    }
}
