<?php

namespace Tests\Feature\Modules\StocktakeSuggestions;

use App\Models\Product;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_module_basic_functionality()
    {
        /** @var Product $product */
        $product = factory(Product::class)->create();
        $warehouse = factory(Warehouse::class)->create();

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

        ray(StocktakeSuggestion::query()->count());
    }
}
