<?php

use App\Models\Inventory;
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
        $warehouses = Warehouse::all()->each(function (Warehouse $warehouse) {
            \App\Models\Product::query()
                ->get()
                ->each(function (\App\Models\Product $product) use ($warehouse) {
                    Inventory::updateOrCreate([
                        'product_id'  => $product->id,
                        'location_id' => $warehouse->code,
                    ],[
                        'quantity' => rand(0, 100)
                    ]);
                });
        });
    }
}
