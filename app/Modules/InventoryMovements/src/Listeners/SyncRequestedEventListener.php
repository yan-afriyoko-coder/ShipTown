<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;

class SyncRequestedEventListener
{
    public function handle()
    {
        PreviousMovementIdJob::dispatch();
        QuantityBeforeJob::dispatch();
//        QuantityDeltaJob::dispatch();
//        QuantityAfterJob::dispatch();
//        InventoryLastMovementIdJob::dispatch();
//        InventoryQuantityJob::dispatch();
    }
}
