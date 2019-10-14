<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrdersControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_update () {

        $order_v1 = [
            'order_number'      => '0123456789',
            "products" => [
                [
                    'sku' => '123',
                    'quantity'     => 2,
                    'price'        => 4,
                ]
            ]
        ];

        $order_v2 = [
            'order_number'      => '0123456789',
            "products" => [
                [
                    'sku' => '123',
                    'quantity'     => 20,
                    'price'        => 4,
                ]
            ]
        ];

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', 'api/orders', $order_v1)
            ->assertStatus(200);

        $product_before = Product::firstOrCreate(["sku" => '123']);

        $this->json('POST', 'api/orders', $order_v2)
            ->assertStatus(200)
            ->assertJsonFragment(['quantity' => $order_v2['products'][0]['quantity']]);

        $product_after = $product_before->fresh();

        $quantity_reserved_diff_actual = $product_after->quantity_reserved - $product_before->quantity_reserved;
        $quantity_reserved_diff_expected = $order_v2['products'][0]['quantity'] - $order_v1['products'][0]['quantity'];

        $this->assertEquals($quantity_reserved_diff_expected, $quantity_reserved_diff_actual);

    }

    public function test_orders_get_route() {

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('GET', 'api/orders')
            ->assertStatus(200);

    }

    public function test_orders_create_and_delete_routes_for_authenticated_user () {

        $data = [
            'order_number'      => '0123456789',
            "products" => [
                [
                    'sku' => '123',
                    'quantity'     => 2,
                    'price'        => 4,
                ],

                [
                    'sku' => '456',
                    'quantity'     => 5,
                    'price'        => 10,
                ],
            ]
        ];

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', 'api/orders', $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'order_number' => $data['order_number']
        ]);

        $this->json('DELETE', 'api/orders/0123456789')
            ->assertStatus(200);

        $this->assertDatabaseMissing('orders', [
            'order_number' => $data['order_number']
        ]);

    }

    public function test_orders_route_for_unauthenticated_user () {

        $data = [
            'orderID'      => '001241',
            "products" => [
                [
                    'sku' => '123',
                    'quantity'     => 2,
                    'price'        => 4,
                ],

                [
                    'sku' => '456',
                    'quantity'     => 5,
                    'price'        => 10,
                ],
            ]
        ];

        $this->json('POST', 'api/orders', [$data])
            ->assertStatus(401);

    }

    public function test_if_missing_order_number_is_not_allowed() {

        $data = [
            //'order_number'      => '001241',
            "products" => [
                [
                    'sku' => '123',
                    'quantity'     => 2,
                    'price'        => 4,
                ],

                [
                    'sku' => '456',
                    'quantity'     => 5,
                    'price'        => 10,
                ],
            ]
        ];


        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', 'api/orders', $data)
            ->assertStatus(422);

    }

    public function test_if_missing_products_section_is_not_allowed() {

        $data = [
            'order_number'      => '001241',
        ];


        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', 'api/orders', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['products']);
    }

    public function test_correct_products_sections() {

        $data = [
            'order_number'      => '001241',
            "products" => [
                [] // blank products record
            ]
        ];

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', 'api/orders', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.sku'])
            ->assertJsonValidationErrors(['products.0.quantity'])
            ->assertJsonValidationErrors(['products.0.price']);
    }

    public function test_if_quantities_are_reserved_when_new_order_created() {

        $order = [
            'order_number'      => '001241',
            "products" => [
                [
                    'sku'       => '0123456',
                    'quantity'  => 2,
                    'price'     => 4,
                ]
            ]
        ];

        Passport::actingAs(
            factory(User::class)->create()
        );

        $product_before = Product::firstOrCreate(["sku" => '0123456']);

        $this->json('POST', 'api/orders', $order)
            ->assertStatus(200);

        $product_after = $product_before->fresh();

        $this->assertEquals($product_after->quantity_reserved, $product_before->quantity_reserved + 2);

    }

    public function test_if_quantities_are_released_when_order_deleted()
    {
        $order = [
            'order_number'      => '0123456789',
            "products" => [
                [
                    'sku'       => '0123456',
                    'quantity'  => 2,
                    'price'     => 4,
                ]
            ]
        ];

        Passport::actingAs(
            factory(User::class)->create()
        );

        $product_before = Product::firstOrCreate(["sku" => '0123456']);

        $this->json('POST', 'api/orders', $order)->assertStatus(200);
        $this->json('DELETE', 'api/orders/0123456789')->assertStatus(200);

        $product_after = $product_before->fresh();

        $this->assertEquals($product_after->quantity_reserved, $product_before->quantity_reserved);
    }

}
