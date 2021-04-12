<?php

namespace Tests\Feature\Http\Controllers\Api\OrderController;

use App\Models\Order;
use App\User;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    /** @test */
    public function test_update_call_returns_ok()
    {
        $order = factory(Order::class)->create();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->putJson(route('orders.update', [$order]), [
            'status_code' => "test_status_code",
            'packer_user_id' => $user->getKey(),
        ]);

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
}
