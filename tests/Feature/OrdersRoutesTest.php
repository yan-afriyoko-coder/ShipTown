<?php

namespace Tests\Feature;

use App\Listeners\PublishSnsMessage;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Event;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\ModelSample;
use Tests\TestCase;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrdersRoutesTest extends TestCase
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

        // create product
        $product_before = $this->json('POST', "api/products", ModelSample::PRODUCT)
            ->assertStatus(200)
            ->getContent();

        // submit first order
        $this->json('POST', 'api/orders', ModelSample::ORDER_01)
            ->assertStatus(200);





        // get product before submitting second order
        $product_before = $this->json('POST', "api/products", ModelSample::PRODUCT)
            ->assertStatus(200)
            ->getContent();

        $product_before = json_decode($product_before, true);

        // submit second order
        $this->json('POST', 'api/orders', ModelSample::ORDER_02)
            ->assertStatus(200);

        // get product after submitting second order
        $product_after = $this->json('POST', "api/products", ModelSample::PRODUCT)
            ->assertStatus(200)
            ->getContent();

        $product_after = json_decode($product_after, true);




        $quantity_reserved_diff_actual = $product_after['quantity_reserved'] - $product_before['quantity_reserved'];

        $quantity_reserved_diff_expected = ModelSample::ORDER_02['products'][0]['quantity'] - ModelSample::ORDER_01['products'][0]['quantity'];

        $this->assertEquals($quantity_reserved_diff_expected, $quantity_reserved_diff_actual);

    }

    public function test_orders_get_route() {

        Event::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('GET', 'api/orders')
            ->assertStatus(200);

    }

    public function test_orders_create_and_delete_routes_for_authenticated_user () {

        Event::fake();

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
