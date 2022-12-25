<?php

namespace Tests\Feature\Http\Controllers\Api\OrderCheckRequestController;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        /** @var Order $order */
        $order = Order::factory()->create();

        $response = $this->postJson(route('order-check-request.store'), [
            'order_id' => $order->getKey()
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'order_id'
            ],
        ]);
    }
}
