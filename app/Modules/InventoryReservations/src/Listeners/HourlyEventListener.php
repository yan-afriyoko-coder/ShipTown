<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityJob;
use App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob;

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
        RecalculateQuantityJob::dispatch();
        RecalculateQuantityReservedJob::dispatch();
    }
}
