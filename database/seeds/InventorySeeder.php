<?php

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Warehouse::all()->each(function (Warehouse $warehouse) {
            Product::query()
                ->get()
                ->each(function (Product $product) use ($warehouse) {
                    $restock_level = rand(0, 100);

                    Inventory::where([
                        'product_id'  => $product->id,
                        'warehouse_id' => $warehouse->getKey(),
                    ])->update([
                        'quantity'      => rand(0, 100),
                        'restock_level' => $restock_level,
                        'reorder_point' => rand(0, $restock_level)
                    ]);
                });
        });
    }
}
