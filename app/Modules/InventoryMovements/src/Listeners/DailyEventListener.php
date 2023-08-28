<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\QuantityAfterJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityDeltaJob;

class DailyEventListener
{
    public function handle()
    {
        QuantityAfterJob::dispatch();
        QuantityDeltaJob::dispatch();
    }
}
