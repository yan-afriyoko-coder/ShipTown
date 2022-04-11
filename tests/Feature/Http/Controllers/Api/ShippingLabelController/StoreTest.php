<?php

namespace Tests\Feature\Http\Controllers\Api\ShippingLabelController;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_store_call_returns_ok()
    {
        $user = factory(User::class)->create();
        $order = factory(Order::class)->create();

        $response = $this->actingAs($user, 'api')
            ->postJson(route('shipping-labels.store'), [
                'shipping_service_code' => 'address_label',
                'order_id' => $order->getKey(),
            ]);

        $response->assertOk();
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
