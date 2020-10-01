<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PacklistOrderTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        factory(Order::class)->create();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/packlist/order');

        $response->assertStatus(200);
    }
}
