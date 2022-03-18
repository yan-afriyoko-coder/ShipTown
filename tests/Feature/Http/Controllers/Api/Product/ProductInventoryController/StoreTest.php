<?php

namespace Tests\Feature\Http\Controllers\Api\Product\ProductInventoryController;

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
        $params = [
            'sku' => $product->sku,
            'location_id' => $warehouse->code,
            'warehouse_code' => $warehouse->code,
        ];

        $response = $this->post("api/product/inventory", $params);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'quantity',
                    'quantity_reserved',
                    'product_id',
                    'location_id',
                    'warehouse_code',
                    'updated_at',
                    'created_at',
                    'id',
                    'quantity_available',
                ]
            ]
        ]);
    }

    public function testIfInvalidSkuIsNotAllowed()
    {
        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();

        $params = [
            'sku' => 0,
            'location_id' => $warehouse->code,
        ];

        $response = $this->post("api/product/inventory", $params);
        $response->assertStatus(404);
    }
}
