<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_route_authenticated_user () {

        $data =
            '{
                "sku": "123456",
                "description": "Blue bag",
                "price": 78
            }';


        Passport::actingAs(
            factory(User::class)->create()
        );


        $this->json('POST', 'api/products', [$data])->assertStatus(200);


    }

    public function test_products_route_unauthenticated_user () {

        $data =
            '{
                "sku": "123456",
                "description": "Blue bag",
                "price": 78
            }';

        $response = $this->json('POST', 'api/products',[
                $data
            ]);

        $response->assertStatus(401);
    }
}
