<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeBasicJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeStocktakeJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        QuantityBeforeBasicJob::dispatch();
        QuantityBeforeJob::dispatch();
        PreviousMovementIdJob::dispatch();
        QuantityBeforeStocktakeJob::dispatch();
    }
}
