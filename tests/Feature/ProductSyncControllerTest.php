<?php

namespace Tests\Feature;

use Faker\Factory;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductSyncControllerTest extends TestCase
{
    /**
     * @return void
     */
    public function test_route()
    {
        $response = $this->get('/products/123456/sync');

        // assert route is protected
        $response->assertStatus(302);
    }

    public function test_route_authenticated()
    {
        Passport::actingAs(factory()->make());

        $response = $this->get("/products/123456/sync");

        $response->assertStatus(200);
    }
}
