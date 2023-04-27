<?php

namespace App\Modules\InventoryQuantityIncoming\src\Listeners;

use App\Modules\InventoryQuantityIncoming\src\Jobs\FixIncorrectQuantityIncomingJob;

class HourlyEventListener
{
    public function handle()
    {
        FixIncorrectQuantityIncomingJob::dispatch();
    }
}
