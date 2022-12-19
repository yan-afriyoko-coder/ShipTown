<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Warehouse::query()
            ->where('code', '!=', DB::raw('999'))
            ->get()
            ->each(function (Warehouse $warehouse) {
                Product::query()
                    ->get()
                    ->each(function (Product $product) use ($warehouse) {
                        $restock_level = rand(0, 100);

                        $locations = 'ABCDEFGHIJKLMNOPRSTUWXYZ';
                        $random_location = $locations[rand(0, strlen($locations) -1)] . rand(10, 50);

                        Inventory::where([
                            'product_id'  => $product->id,
                            'warehouse_id' => $warehouse->getKey(),
                        ])->update([
                            'quantity'          => rand(0, 210),
                            'quantity_incoming' => rand(0, 210),
                            'restock_level'     => $restock_level,
                            'reorder_point'     => rand(0, $restock_level),
                            'shelve_location'   => $random_location
                        ]);
                    });
            });
    }
}
