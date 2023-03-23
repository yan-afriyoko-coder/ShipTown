<?php

namespace Tests\Feature\Http\Controllers\Api\RestockingController;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        ray()->showApp();

        Warehouse::factory()->create(['id' => 2]);
        Product::factory()->create();

        /** @var User $user */
        $user = User::factory()->create();

        Inventory::query()->update(['quantity' => 1]);

        $response = $this->actingAs($user, 'api')->getJson(route('api.restocking.index'));

        ray($response->json());

        $response->assertOk();

        $this->assertCount(2, $response->json('data'));

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'warehouse_code',
                    'product_sku',
                    'product_name',
                    'quantity_required',
                    'quantity_available',
                    'quantity_incoming',
                    'reorder_point',
                    'restock_level',
                    'warehouse_quantity',
                ],
            ],
        ]);
    }
}
