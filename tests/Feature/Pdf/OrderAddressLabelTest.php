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
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfRouteIsProtected()
    {
        $response = $this->get('pdf/orders/123/address_label');

        $response->assertStatus(302);
    }
}
