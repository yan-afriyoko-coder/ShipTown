<?php

namespace Tests\Feature\Http\Controllers\Api\Order\OrderProductShipmentController;

use App\Models\OrderProduct;
use App\Models\OrderProductShipment;
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
        /** @var OrderProduct $orderProduct */
        $orderProduct = factory(OrderProduct::class)->create();

        $response = $this->postJson('/api/orders/products/shipments', [
            'order_product_id' => $orderProduct->getKey(),
            'quantity_shipped' => $orderProduct->quantity_to_ship,
        ]);

        $response->assertSuccessful();
    }
}
