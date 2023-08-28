<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;

class EveryFiveMinutesEventListener
{
    public function handle()
    {
        QuantityBeforeJob::dispatch();
    }
}
