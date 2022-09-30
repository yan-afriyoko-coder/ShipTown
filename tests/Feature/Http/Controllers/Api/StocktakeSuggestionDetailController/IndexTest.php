<?php

namespace Tests\Feature\Http\Controllers\Api\StocktakeSuggestionDetailController;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create();

        $product = factory(Product::class)->create();
        $warehouse = factory(Warehouse::class)->create();

        $inventory = Inventory::query()->where([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
            ])
            ->first();

        StocktakeSuggestion::query()->create([
            'inventory_id' => $inventory->id,
            'points' => 1,
            'reason' => 'test',
        ]);

        $response = $this->actingAs($user, 'api')->getJson(route('stocktake-suggestions-details.index'));

        $response->assertOk();

        $this->assertCount(1, $response->json('data'), 'No records returned');

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'points',
                    'reason'
                ],
            ],
        ]);
    }
}
