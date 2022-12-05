<?php

namespace Tests\Feature\Modules\StocktakeSuggestions;

use App\Models\Product;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $inventory = $product->inventory()->first();

        StocktakeSuggestion::query()->create([
            'product_id' => $product->id,
            'inventory_id' => $inventory->id,
            'points' => 1,
            'reason' => 'test',
        ]);

        StocktakeSuggestion::query()->create([
            'product_id' => $product->id,
            'inventory_id' => $inventory->id,
            'points' => 1,
            'reason' => 'test2',
        ]);

        $this->assertCount(2, StocktakeSuggestion::query()->get());
    }
}
