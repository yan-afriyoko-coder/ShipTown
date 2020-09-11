<?php

namespace Tests\Feature\Routes;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderShipmentsTest extends TestCase
{
    public function testAuthenticatedGetRoute()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/order/shipments');

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUnauthenticated()
    {
        $response = $this->get('/api/order/shipments');

        $response->assertStatus(302);
    }
}
