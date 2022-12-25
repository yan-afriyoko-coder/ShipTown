<?php

namespace Tests\Feature\Http\Controllers\Api\ShipmentControllerNew;

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
        $user = User::factory()->create()->assignRole('user');
        $this->actingAs($user, 'api');
    }

    public function test_successful()
    {
        $order = Order::factory()->create();

        $response = $this->postJson(route('new.shipments.store'), [
            'order_id' => $order->getKey(),
            'shipping_number' => 'test',
            'carrier' => 'dpd_uk',
            'service' => 'overnight',
        ]);

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
    }

    /** @test */
    public function test_empty()
    {
        $response = $this->postJson(route('new.shipments.store'), []);

        $response->assertStatus(422);
    }
}
