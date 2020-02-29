<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\Feature\AuthorizedUserTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsRoutesTest extends TestCase
{
    /**
     * @return void
     */
    public function test_get_products_route_authenticated()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/products');

        $response->assertStatus(200);
    }

    public function test_get_products_route_unauthenticated()
    {
        $response = $this->get('/products');

        $response->assertStatus(302);
    }
}
