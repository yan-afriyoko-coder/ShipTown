<?php

namespace Tests\Feature;

use App\Managers\ProductManager;
use App\User;
use Laravel\Passport\Passport;
use Tests\ModelSample;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(
            factory(User::class)->create()
        );
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_reserves_correctly()
    {
        $quantity_to_reserve = 10;

        $product_before = $this->json('POST', 'api/products', ModelSample::PRODUCT)
            ->assertStatus(200)
            ->getContent();

        $product_before = json_decode($product_before, true);


        ProductManager::reserve(ModelSample::PRODUCT['sku'], $quantity_to_reserve, 'Reservation test');


        $product_after = $this->json('POST', 'api/products', ModelSample::PRODUCT)
            ->assertStatus(200)
            ->getContent();

        $product_after = json_decode($product_after, true);

        $quantity_reserved_diff = $product_after['quantity_reserved'] - $product_before['quantity_reserved'];

        $this->assertEquals($quantity_to_reserve, $quantity_reserved_diff);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_releases_quantity_correctly()
    {
        $quantity_to_release = 10;

        $product_before = $this->json('POST', 'api/products', ModelSample::PRODUCT)
            ->assertStatus(200)
            ->getContent();

        $product_before = json_decode($product_before, true);


        ProductManager::release(ModelSample::PRODUCT['sku'], $quantity_to_release, 'Reservation test');


        $product_after = $this->json('POST', 'api/products', ModelSample::PRODUCT)
            ->assertStatus(200)
            ->getContent();

        $product_after = json_decode($product_after, true);

        $quantity_reserved_diff =  $product_before['quantity_reserved'] - $product_after['quantity_reserved'];

        $this->assertEquals($quantity_to_release, $quantity_reserved_diff);
    }
}
