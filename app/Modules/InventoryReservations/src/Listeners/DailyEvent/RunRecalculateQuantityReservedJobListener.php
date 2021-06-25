<?php

namespace App\Modules\InventoryReservations\src\Listeners\DailyEvent;

use App\Events\DailyEvent;
use App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob;

class RunRecalculateQuantityReservedJobListener
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
        RecalculateQuantityReservedJob::dispatch();
    }
}
