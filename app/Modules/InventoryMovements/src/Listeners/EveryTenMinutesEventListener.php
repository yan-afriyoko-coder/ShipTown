<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        QuantityBeforeJob::dispatch();
//        PreviousMovementIdJob::dispatch();
    }
}
