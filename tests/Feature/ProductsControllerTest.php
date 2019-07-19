<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{

    public function test_products_route_authenticated_user () {

        $data =
            '{
                "sku": "123456",
                "description": "Blue bag",
                "price": 78
            }';


        $response = $this->withHeaders([
            'Authorization'=>env('TEST_AUTH'),
            ])->json('POST', 'api/products',[
                $data
            ]);

        $response->assertStatus(200);

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
