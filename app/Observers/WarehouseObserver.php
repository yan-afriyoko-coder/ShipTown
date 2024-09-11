<?php

namespace App\Observers;

use App\Models\Warehouse;
use App\Modules\Maintenance\src\Jobs\Products\EnsureAllInventoryRecordsExistsJob;
use App\Modules\Maintenance\src\Jobs\Products\EnsureAllProductPriceRecordsExistsJob;
use App\Modules\Maintenance\src\Jobs\UpdateInventoryWarehouseCodeJob;
use App\Modules\Maintenance\src\Jobs\UpdateProductPriceWarehouseCodeJob;

class WarehouseObserver
{
    public function created(Warehouse $warehouse): void
    {
        EnsureAllInventoryRecordsExistsJob::dispatch();
        EnsureAllProductPriceRecordsExistsJob::dispatch();
    }

    public function updated(Warehouse $warehouse): void
    {
        UpdateInventoryWarehouseCodeJob::dispatch($warehouse);
        UpdateProductPriceWarehouseCodeJob::dispatch($warehouse);
    }
}
