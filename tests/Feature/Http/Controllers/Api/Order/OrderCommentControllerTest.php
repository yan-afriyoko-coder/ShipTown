<?php

namespace Tests\Feature\Http\Controllers\Api\Order;

use App\Models\Order;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Order\OrderCommentController
 */
class OrderCommentControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('comments.index'));

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
        $attributes = [
            'order_id' => $order->getKey(),
            'comment' => 'Test comment',
        ];

        $response = $this->actingAs($user, 'api')->postJson(route('comments.store'), $attributes);

        $response->assertOk();

        $response->assertJsonStructure([
//            'meta',
//            'links',
            'data' => [
                '*' => [
                    'order_id',
                    'user_id',
                    'comment',
                ],
            ],
        ]);
    }

    // test cases...
}
