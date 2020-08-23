<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrdersRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function testIsPickedFilterFalse()
    {
        Order::query()->delete();

        factory(Order::class)->create([
            'picked_at' => now()
        ]);

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/orders?filter[is_picked]=false');

        $response->assertJsonStructure([
            "current_page",
            "data" => [
                "*" => [
                    "id",
                ]
            ],
            "total",
        ]);

        $this->assertEquals(0, $response->json('total'));
    }

    public function testIsPickedFilterTrue()
    {
        Order::query()->delete();

        factory(Order::class)->create([
            'picked_at' => now()
        ]);

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/orders?filter[is_picked]=true');

        $response->assertJsonStructure([
            "current_page",
            "data" => [
                "*" => [
                    "id",
                ]
            ],
            "total",
        ]);

        $this->assertEquals(1, $response->json('total'));
    }

    public function testOrdersGetRoute()
    {
        Event::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('GET', 'api/orders')
            ->assertStatus(200);
    }

    public function testOrdersCreateAndDeleteRoutesForAuthenticatedUser()
    {
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

    public function testOrdersRouteForUnauthenticatedUser()
    {
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

    public function testIfMissingOrderNumberIsNotAllowed()
    {
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

    public function testIfMissingProductsSectionIsNotAllowed()
    {

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

    public function testCorrectProductsSections()
    {
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

//    public function test_if_quantities_are_reserved_when_new_order_created() {
//
//        $order = [
//            'order_number'      => '001241',
//            "products" => [
//                [
//                    'sku'       => '0123456',
//                    'quantity'  => 2,
//                    'price'     => 4,
//                ]
//            ]
//        ];
//
//        Passport::actingAs(
//            factory(User::class)->create()
//        );
//
//        $product_before = Product::firstOrCreate(["sku" => '0123456']);
//
//        $this->json('POST', 'api/orders', $order)
//            ->assertStatus(200);
//
//        $product_after = $product_before->fresh();
//
//        $this->assertEquals($product_after->quantity_reserved, $product_before->quantity_reserved + 2);
//
//    }

    public function testIfQuantitiesAreReleasedWhenOrderDeleted()
    {
        Event::fake();

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
