<?php

namespace Tests\Feature\web;

use Tests\Feature\AuthorizedUserTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsRouteTest extends TestCase
{
    use AuthorizedUserTestCase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_products_route_works()
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
    }
}
