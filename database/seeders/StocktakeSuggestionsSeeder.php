<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\StocktakeSuggestion;
use Illuminate\Database\Seeder;

class StocktakeSuggestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventories = Inventory::query()->inRandomOrder()->limit(10)->get();

        $inventories->each(function (Inventory $inventory) {
            $stocktakeSuggestion = new StocktakeSuggestion();
            $stocktakeSuggestion->product_id  = $inventory->product_id;
            $stocktakeSuggestion->inventory_id  = $inventory->getKey();
            $stocktakeSuggestion->points  = 20;
            $stocktakeSuggestion->reason  = 'Manual stoctake request';
            $stocktakeSuggestion->save();
        });
    }
}
