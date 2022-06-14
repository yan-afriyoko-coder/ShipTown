<?php

namespace Tests\Feature\Http\Controllers\Api\InventoryMovementController;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_if_store_call_returns_ok()
    {
        $user = factory(User::class)->create();

        $warehouse = factory(Warehouse::class)->create();
        $product = factory(Product::class)->create();

        $data = [
            'warehouse_id' => $warehouse->getKey(),
            'product_id' => $product->getKey(),
            'quantity' => 1,
            'description' => 'test',
        ];

        $response = $this->actingAs($user, 'api')->postJson(route('inventory-movement.store'), $data);

        ray($response->json());

        $response->assertOk();

        $this->assertCount(1, $response->json('data'), 'No records returned');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id'
                ],
            ],
        ]);
    }
}
