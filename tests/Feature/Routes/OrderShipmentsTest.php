<?php

namespace Tests\Feature\Routes;

use App\Models\Order;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OrderShipmentsTest extends TestCase
{
    public function testPostRoute()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $order = factory(Order::class)->create();

        $response = $this->post('/api/order/shipments', [
            'order_id' => $order['id'],
            'shipping_number' => '123',
        ]);

        $response->assertStatus(201);
    }

    public function testGetRoute()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/order/shipments');

        $response->assertStatus(200);
    }
}
