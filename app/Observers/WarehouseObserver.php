<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Warehouse;

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
        $this->insertInventoryRecords($warehouse);
        $this->insertPricingRecords($warehouse);
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

    /**
     * @param Warehouse $warehouse
     */
    private function insertInventoryRecords(Warehouse $warehouse): void
    {
        $inventoryRecords = Product::all(['id'])
            ->map(function (Product $product) use ($warehouse) {
                return [
                    'warehouse_id'    => $warehouse->getKey(),
                    'product_id'      => $product->getKey(),
                    'location_id'     => $warehouse->code,
                    'warehouse_code'  => $warehouse->code,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            });

        Inventory::query()->insert($inventoryRecords->toArray());
    }

    /**
     * @param Warehouse $warehouse
     */
    private function insertPricingRecords(Warehouse $warehouse): void
    {
        $productPriceRecords = Product::all(['id'])
            ->map(function (Product $product) use ($warehouse) {
                return [
                    'product_id'     => $product->getKey(),
                    'location_id'    => $warehouse->code,
                    'warehouse_code' => $warehouse->code,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            });

        ProductPrice::query()->insert($productPriceRecords->toArray());
    }
}
