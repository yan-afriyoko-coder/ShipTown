<?php

namespace Tests\Feature\Routes;

use App\Models\Order;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OrderPackedTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $order = factory(Order::class)->create([
            'status_code' => 'packing_warehouse',
            'packer_user_id' => $user->getKey(),
        ]);

        $response = $this->put('/api/orders/'.$order->getKey(), [
            'is_packed' => true,
        ]);

        $response->assertStatus(200);
    }
}
