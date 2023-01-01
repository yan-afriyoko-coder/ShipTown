<?php

namespace Tests\Feature\Http\Controllers\Api\InventoryController;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        $product = Product::factory()->create();

        $inventory = Inventory::first();

        $params = [
            'id' => $inventory->getKey(),
            'shelve_location' => 'test',
        ];

        $response = $this->post(route('api.inventory.index'), $params);

        ray($response->json());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'quantity',
                    'quantity_reserved',
                    'product_id',
                    'warehouse_code',
                    'updated_at',
                    'created_at',
                    'id',
                    'quantity_available',
                ]
            ]
        ]);
    }
}
