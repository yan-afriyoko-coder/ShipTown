<?php

namespace Tests\Feature\Routes\Api;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OrderProductsRoutesTest extends TestCase
{
//    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAuthenticatedGet()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/order/products');

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUnauthenticatedGet()
    {
        $response = $this->get('/api/order/products');

        $response->assertStatus(302);
    }
}
