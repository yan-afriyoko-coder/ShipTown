<?php

namespace Tests\Feature\Http\Controllers\Api\PacklistOrderController;

use App\Models\Order;
use App\Models\OrderStatus;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create();

        factory(OrderStatus::class)->create([
            'code' => 'packing',
            'name' => 'packing',
            'order_active' => true,
            'order_on_hold' => false,
        ]);

        factory(Order::class)->create(['status_code' => 'packing']);

        $response = $this->actingAs($user, 'api')->getJson(route('packlist.order.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }
}
