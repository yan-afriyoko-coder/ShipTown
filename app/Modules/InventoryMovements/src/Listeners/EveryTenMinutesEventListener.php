<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\InventoryLastMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\InventoryQuantityJob;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;
use App\Modules\InventoryMovements\src\Jobs\SequenceNumberJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        QuantityBeforeJob::dispatch();
        PreviousMovementIdJob::dispatch();
        InventoryQuantityJob::dispatch();
        InventoryLastMovementIdJob::dispatch();
        SequenceNumberJob::dispatch();
    }
}
