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
        EnsureAllInventoryRecordsExistsJob::dispatchAfterResponse();
        EnsureAllProductPriceRecordsExistsJob::dispatchAfterResponse();
    }

    public function updated(Warehouse $warehouse)
    {
        Inventory::query()
            ->where(['location_id' => $warehouse->getOriginal('code')])
            ->update(['warehouse_code' => $warehouse->code, 'location_id' => $warehouse->code]);

        ProductPrice::query()
            ->where(['location_id' => $warehouse->getOriginal('code')])
            ->update(['warehouse_code' => $warehouse->code, 'location_id' => $warehouse->code]);
    }
}
