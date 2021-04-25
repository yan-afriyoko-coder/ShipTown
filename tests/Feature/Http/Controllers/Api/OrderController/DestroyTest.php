<?php

namespace Tests\Feature\Http\Controllers\Api\OrderController;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Order
     */
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->order = factory(Order::class)->create();

        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');

    }

    /** @test */
    public function test_destroy_call_returns_ok()
    {
        $this->delete( 'api/orders/' . $this->order->order_number)->assertStatus(200);

        $this->assertDatabaseMissing('orders', ['order_number' => $this->order->order_number]);
    }
}
