<?php

namespace Tests\Feature;

use Tests\Feature\AuthorizedUserTestCase;
use Tests\ModelSample;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductGetRouteTest extends TestCase
{
    use AuthorizedUserTestCase;

    /**
     * @return void
     */
    public function test_successful_get_products_route()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
    }
}
