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

    public function test_products_response_structure()
    {
        // We will send item to make sure
        // at lease one item exists
        $this->json("POST", '/api/products', ModelSample::PRODUCT);

        $response = $this->get('/api/products');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "current_page",
            "data" => [
                "*" => [
                    "sku",
                    "name",
                    "price",
                    "sale_price",
                    "sale_price_start_date",
                    "sale_price_end_date",
                    "quantity",
                    "quantity_reserved",
                    "quantity_available"
                ]
            ],
            "total",
        ]);
    }

    /**
     * @return void
     */
    public function test_successful_get_products_route()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
    }
}
