<?php

namespace Tests\Feature\Http\Controllers\Api\ActivityController;

use App\Models\Order;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        $order = factory(Order::class)->create();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')
            ->postJson(route('activities.store', [
                'subject_type' => 'order',
                'subject_id' => $order->getKey(),
                'description' => 'test message',
            ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'success',
                'message',
            ],
        ]);
    }
}
