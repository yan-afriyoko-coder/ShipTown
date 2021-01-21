<?php

namespace Tests\Feature\Http\Controllers\Api\Order;

use App\Models\OrderProduct;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Order\OrderProductController
 */
class OrderProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        factory(OrderProduct::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('order.products.index', [
            'include'=> [
                'order',
                'product',
                'product.aliases'
            ]
        ]));

        $response->assertOk();

        $this->assertNotEquals(0, $response->json('meta.total'));

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id',
                    'order_id',
                    'product_id',
                    'sku_ordered',
                    'name_ordered',
                    'quantity_ordered',
                    'quantity_picked',
                    'quantity_shipped',
                    'order' => [],
                    'product' => [
                        'aliases' => []
                    ],
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        $user = factory(User::class)->create();
        $orderProduct = factory(OrderProduct::class)->create();

        $response = $this->actingAs($user, 'api')->putJson(route('order.products.update', [$orderProduct]), [
            'quantity_shipped' => $orderProduct->quantity_to_ship
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'order_id',
                'product_id',
                'sku_ordered',
                'name_ordered',
                'quantity_ordered',
                'quantity_picked',
                'quantity_shipped',
            ]
        ]);
    }

    // test cases...
}
