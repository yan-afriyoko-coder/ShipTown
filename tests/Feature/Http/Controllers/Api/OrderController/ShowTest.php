<?php

namespace Tests\Feature\Http\Controllers\Api\OrderController;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_call_returns_ok()
    {
        $order = factory(Order::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('orders.show', $order));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'shipping_address_id',
                'order_number',
                'status_code',
                'is_active',
                'total',
                'total_paid',
                'shipping_method_code',
                'shipping_method_name',
                'order_placed_at',
                'order_closed_at',
                'product_line_count',
                'picked_at',
                'packed_at',
                'packer_user_id',
                'is_picked',
                'is_packed',
                'age_in_days',
                'created_at',
            ],
        ]);
    }

    public function test_show_call_not_found()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('orders.show', 0));
        $response->assertStatus(404);
    }
}
