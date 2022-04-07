<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\ProductPrice;
use App\Models\Warehouse;
use App\Modules\Maintenance\src\Jobs\Products\EnsureAllInventoryRecordsExistsJob;
use App\Modules\Maintenance\src\Jobs\Products\EnsureAllProductPriceRecordsExistsJob;

class WarehouseObserver
{
    /**
     * Handle the product "created" event.
     *
     * @param Warehouse $warehouse
     * @return void
     */
    public function created(Warehouse $warehouse)
    {
        EnsureAllInventoryRecordsExistsJob::dispatch();
        EnsureAllProductPriceRecordsExistsJob::dispatch();
    }

    public function updated(Warehouse $warehouse)
    {
        Inventory::query()
            ->where(['warehouse_id' => $warehouse->getKey()])
            ->update(['warehouse_code' => $warehouse->code, 'location_id' => $warehouse->code]);

        ProductPrice::query()
            ->where(['warehouse_id' => $warehouse->getKey()])
            ->update(['warehouse_code' => $warehouse->code, 'location_id' => $warehouse->code]);
    }
}
