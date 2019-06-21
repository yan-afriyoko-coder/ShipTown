<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{

    public function test_products_route () {

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

    public function test_if_unauthenticated_user_is_not_allowed () {

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
