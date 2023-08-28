<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\InventoryLastMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\InventoryQuantityJob;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityAfterJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityDeltaJob;

class DailyEventListener
{
    public function handle()
    {
        PreviousMovementIdJob::dispatch();

        QuantityBeforeJob::dispatch();
        QuantityAfterJob::dispatch();
        QuantityDeltaJob::dispatch();

        InventoryLastMovementIdJob::dispatch();
        InventoryQuantityJob::dispatch();
    }
}
