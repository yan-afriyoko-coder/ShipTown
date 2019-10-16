<?php

namespace Tests\Feature\routes;

use App\User;
use Laravel\Passport\Passport;
use Tests\ModelSample;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsTest extends TestCase
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
    public function test_POST_route()
    {
        $response = $this->post('api/products', ModelSample::PRODUCT)
            ->assertStatus(200)
            ->assertJson(['sku' => ModelSample::PRODUCT['sku']])
            ->assertJson(['name' => ModelSample::PRODUCT['name']]);
    }
}
