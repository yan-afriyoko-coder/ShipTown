<?php

namespace Tests\Feature\Http\Controllers\Api\Product\ProductInventoryController;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();

        $product = factory(Product::class)->create();

        $inventory = Inventory::first();

        $params = [
            'id' => $inventory->getKey(),
            'shelve_location' => 'test',
        ];

        $response = $this->post("api/product/inventory", $params);

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
