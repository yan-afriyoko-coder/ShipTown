<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    public function testIfProductFoundUsingAlias()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/products?q=45');

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'sku',
                    'name',
                    'price',
                    'sale_price',
                    'sale_price_start_date',
                    'sale_price_end_date',
                    'quantity',
                    'quantity_reserved',
                    'quantity_available',
                ],
            ],
            'total',
        ]);
    }

    /**
     * @return void
     */
    public function testGetProductsRouteAuthenticated()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/products');

        $response->assertStatus(200);
    }

    public function testGetProductsRouteUnauthenticated()
    {
        $response = $this->get('/products');

        $response->assertStatus(302);
    }

    public function testGetProductsResponseStructure()
    {
        Passport::actingAs(
            factory(User::class)->make()
        );

        $product = factory(Product::class)->make();

        $this->json('POST', '/api/products', $product->toArray());

        $response = $this->get('/api/products');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'sku',
                    'name',
                    'price',
                    'sale_price',
                    'sale_price_start_date',
                    'sale_price_end_date',
                    'quantity',
                    'quantity_reserved',
                    'quantity_available',
                ],
            ],
            'total',
        ]);
    }

    public function testIfProductsSyncReturns404WhenProductNotFound()
    {
        Passport::actingAs(
            factory(User::class)->make()
        );

        $response = $this->get('api/products/0/sync');

        $response->assertNotFound();
    }
}
