<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\InventoryQuantityCheckJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityAfterCheckJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeCheckJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityDeltaCheckJob;

class EveryHourEventListener
{
    public function handle()
    {
        QuantityAfterCheckJob::dispatch();
        QuantityBeforeCheckJob::dispatch();
        QuantityDeltaCheckJob::dispatch();
        InventoryQuantityCheckJob::dispatch();
    }
}
