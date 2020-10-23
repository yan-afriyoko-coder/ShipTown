<?php

namespace Tests\Feature\Routes;

use App\Models\Order;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OrderCommentsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPost()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $order = factory(Order::class)->create();

        $data = [
            'order_id' => $order->getKey(),
            'comment' => 'Test comment'
        ];

        $response = $this->post('/api/order/comments', $data);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'order_id',
                    'user_id',
                    'comment',
                ]
            ],
        ]);
    }
}
