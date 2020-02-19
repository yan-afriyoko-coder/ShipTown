<?php

namespace Tests\Feature;

use App\User;
use Faker\Factory;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductSyncControllerTest extends TestCase
{
    public function test_if_404_returned_when_product_not_found
    {
        Passport::actingAs(
            factory(User::class)->make()
        );

        $response = $this->get("/products/0/sync");

        $response->assertNotFound();
    }

    public function test_route()
    {
        $response = $this->get('/products/123456/sync');

        // assert route is protected
        $response->assertStatus(302);
    }

    public function test_route_authenticated()
    {
        Passport::actingAs(
            factory(User::class)->make()
        );

        $response = $this->get("/products/123456/sync");

        $response->assertStatus(200);
    }
}
