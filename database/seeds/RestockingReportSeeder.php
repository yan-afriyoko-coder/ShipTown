<?php

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
            ?? factory(Warehouse::class)->create(['code' => '99']);

        $destination_warehouse = Warehouse::whereCode('DUB')->first()
            ?? factory(Warehouse::class)->create(['code' => 'DUB']);

        $products = factory(Product::class, 10)->create();

        $products->each(function (Product $product) use ($source_warehouse, $destination_warehouse) {
            InventoryService::adjustQuantity(
                $product->inventory($source_warehouse->code)->first(),
                rand(3, 13),
                'stocktake for Restocking report sample'
            );

            InventoryService::adjustQuantity(
                $product->inventory($destination_warehouse->code)->first(),
                rand(-13, -3),
                'simulate stock sold, but not received yet'
            );
        });
    }
}
