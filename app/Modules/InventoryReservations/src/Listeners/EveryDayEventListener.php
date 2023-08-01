<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob;

class EveryDayEventListener
{
    public function handle()
    {
        RecalculateQuantityReservedJob::dispatch();
    }
}
