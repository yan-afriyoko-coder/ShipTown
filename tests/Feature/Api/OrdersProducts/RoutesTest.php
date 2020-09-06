<?php

namespace Tests\Feature\Api\OrdersProducts;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoutesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAuthorisedGetRequest()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/orders/products');

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUnauthorisedGetRequest()
    {
        $response = $this->get('/api/orders/products');

        $response->assertStatus(302);
    }
}
