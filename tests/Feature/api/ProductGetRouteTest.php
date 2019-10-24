<?php

namespace Tests\Feature\api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductGetRouteTest extends TestCase
{
    /**
     * @return void
     */
    public function test_successful_get_products_route()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
    }
}
