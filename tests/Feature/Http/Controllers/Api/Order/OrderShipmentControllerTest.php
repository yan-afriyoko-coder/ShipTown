<?php

namespace Tests\Feature\Http\Controllers\Api\Order;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Order\OrderShipmentController
 */
class OrderShipmentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('shipments.index'));

        $response->assertOk();
        $response->assertJsonStructure([
//            'meta',
//            'links',
            'data' => [
                '*' => [
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $user = factory(User::class)->create();
        $order = factory(Order::class)->create();

        $response = $this->actingAs($user, 'api')->postJson(route('shipments.store'), [
            'order_id' => $order['id'],
            'shipping_number' => '123',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    // test cases...
}
