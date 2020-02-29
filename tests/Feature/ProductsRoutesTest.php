<?php

namespace Tests\Feature;

use App\Models\Product;
use App\User;
use Laravel\Passport\Passport;
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


    public function test_get_products_response_structure()
    {
        Passport::actingAs(
            factory(User::class)->make()
        );

        $product = factory(Product::class)->make();

        $this->json("POST", '/api/products', $product->toArray());

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

    public function test_if_products_sync_returns_404_when_product_not_found()
    {
        Passport::actingAs(
            factory(User::class)->make()
        );

        $response = $this->get("api/products/0/sync");

        $response->assertNotFound();
    }

    public function test_products_sync_route_unauthenticated()
    {
        $response = $this->get('api/products/0/sync');

        // assert route is protected
        $response->assertStatus(302);

    }

    public function test_product_sync_route_authenticated()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $product = \factory(Product::class)->create();

        $response = $this->get("api/products/$product->sku/sync");

        $response->assertOk();
    }
}
