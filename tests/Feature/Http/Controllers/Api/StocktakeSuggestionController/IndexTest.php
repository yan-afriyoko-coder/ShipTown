<?php

namespace Tests\Feature\Http\Controllers\Api\StocktakeSuggestionController;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create();

        factory(Product::class)->create();
        factory(Warehouse::class)->create();

        factory(StocktakeSuggestion::class)->create(
            [
                'inventory_id' => Inventory::first()->getKey(),
                'points' => 1,
                'reason' => 'test',
            ]
        );

        $response = $this->actingAs($user, 'api')->getJson(route('stocktake-suggestions.index'));

        ray($response->json());

        $response->assertOk();

        $this->assertEquals(1, $response->json('meta.total'), 'No records returned');

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id'
                ],
            ],
        ]);
    }
}
