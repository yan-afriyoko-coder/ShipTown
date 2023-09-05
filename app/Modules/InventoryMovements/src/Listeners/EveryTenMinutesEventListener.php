<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\FirstMovementAtJob;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeSinceLastStocktakeJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeStocktakeJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        QuantityBeforeJob::dispatch();
        QuantityBeforeSinceLastStocktakeJob::dispatch();
        PreviousMovementIdJob::dispatch();
        QuantityBeforeStocktakeJob::dispatch();
        FirstMovementAtJob::dispatch();
    }
}
