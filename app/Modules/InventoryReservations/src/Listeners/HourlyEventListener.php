<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Modules\InventoryReservations\src\Jobs\DetectAndFixIncorrectQuantityJob;
use App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob;

class HourlyEventListener
{
    public function handle()
    {
        DetectAndFixIncorrectQuantityJob::dispatch();
        RecalculateQuantityReservedJob::dispatch();
    }
}
