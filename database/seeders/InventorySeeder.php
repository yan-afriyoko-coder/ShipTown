<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
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
                        $restock_level = Arr::random([1, 6, 6, 6, 12, 12, 12, 12, 24, 24, 24, 36, 72]);

                        $locations = 'ABCDEFGHIJKLMNOPRSTUWXYZ';
                        $random_location = $locations[rand(0, strlen($locations) -1)] . rand(10, 50);

                        $inventory = Inventory::find($product->id, $warehouse->id);

                        $inventory->update([
                            'restock_level'     => $restock_level,
                            'reorder_point'     => round($restock_level / 3),
                            'shelve_location'   => $random_location
                        ]);

                        InventoryService::adjust($inventory, $inventory->restock_level, ['description' => 'First delivery']);
                    });
            });
    }
}
