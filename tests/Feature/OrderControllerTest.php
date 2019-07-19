<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    public function test_orders_route () {

        $data =
            '{
                "orderID": "001241",
                "sku": "123456",
                "quantity": 9,
                "pricePerUnit": 78,
                "priceTotal": 702
            }';


        $response = $this->withHeaders([
            'Authorization'=>env('TEST_AUTH'),
            ])->json('POST', 'api/orders',[
                $data
            ]);

        $response->assertStatus(200);

    }

    public function test_if_unauthenticated_user_is_not_allowed () {

        $data =
            '{
                "orderID": "001241",
                "sku": "123456",
                "quantity": 9,
                "pricePerUnit": 78,
                "priceTotal": 702
            }';

        $response = $this->json('POST', 'api/orders',[
                $data
            ]);

        $response->assertStatus(401);
    }
}
