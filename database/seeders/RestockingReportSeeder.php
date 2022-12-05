<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class RestockingReportSeeder extends Seeder
{
    public function run()
    {
        /** @var Warehouse $destination_warehouse */
        /** @var Warehouse $source_warehouse */

        $source_warehouse = Warehouse::whereCode('99')->first()
            ?? Warehouse::factory()->create(['code' => '99']);

        $products = Product::factory()->count(10)->create();

        $products->each(function (Product $product) use ($source_warehouse) {
            InventoryService::adjustQuantity(
                $product->inventory($source_warehouse->code)->first(),
                rand(30, 100),
                'stocktake for Restocking report sample'
            );

//            InventoryService::adjustQuantity(
//                $product->inventory($destination_warehouse->code)->first(),
//                rand(-13, -3),
//                'simulate stock sold, but not received yet'
//            );
        });

        $destination_warehouse = Warehouse::whereCode('DUB')->first()
            ?? Warehouse::factory()->create(['code' => 'DUB']);

        Inventory::query()->where([
            'warehouse_code' => $destination_warehouse->code,
        ])->update([
            'quantity' => 0,
            'quantity_incoming' => 0,
            'reorder_point' => 0,
            'restock_level' => 10,
        ]);
    }
}
