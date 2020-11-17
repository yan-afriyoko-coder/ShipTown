<?php

namespace Tests\Feature\Routes\Order;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStructure()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/order/products?'.
            '&include=order,product,product.aliases');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "meta",
            "links",
            "data" => [
                "*" => [
                    "id",
                    "order_id",
                    "product_id",
                    "sku_ordered",
                    "name_ordered",
                    "quantity_ordered",
                    "quantity_picked",
                    "quantity_shipped",
                    "order",
                    "product"
                ]
            ]
        ]);

//        dd($response->json());
    }
}
