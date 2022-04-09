<?php

namespace Tests\Feature\Http\Controllers\Api\Product\ProductInventoryController;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create();

        factory(Warehouse::class)->create();
        factory(Product::class)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson(route('inventory.index', [
                'include' => [
                    'product',
                ],
            ]));

        $response->assertOk();

        $this->assertGreaterThan(0, $response->json('meta.total'), 'No records returned');

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'product_id',
                    'warehouse_id',
                    'quantity_available',
                    'quantity',
                    'quantity_reserved',
                    'restock_level',
                    'reorder_point',
                    'quantity_required',
                    'product' => [],
                ],
            ],
        ]);
    }
}
