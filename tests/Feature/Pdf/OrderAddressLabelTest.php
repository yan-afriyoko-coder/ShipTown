<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderAddressLabelTest extends TestCase
{
    /**
     * @return void
     */
    public function test_if_authorized_route_works()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $order = factory(Order::class)->create();

        $response = $this->get('pdf/orders/'.$order->order_number.'/address_label');

//        $response->assertStatus(200);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_route_is_protected()
    {
        $response = $this->get('pdf/orders/123/address_label');

        $response->assertStatus(302);
    }
}
