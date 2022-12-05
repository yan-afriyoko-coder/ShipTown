<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\OrderStatusController;

use App\Models\OrderStatus;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_destroy_call_returns_ok()
    {
        $orderStatus = OrderStatus::create([
            'name' => 'testing',
            'code' => 'testing',
            'order_active' => 0,
            'sync_ecommerce' => 0,
        ]);

        $response = $this->delete(route('api.settings.order-statuses.destroy', $orderStatus));
        $response->assertOk();
    }

    public function test_cannot_delete_order_active()
    {
        $orderStatus = OrderStatus::create([
            'name' => 'testing',
            'code' => 'testing',
            'order_active' => 1,
            'sync_ecommerce' => 0,
        ]);

        $response = $this->delete(route('api.settings.order-statuses.destroy', $orderStatus));
        $response->assertStatus(401);
    }

    public function test_cannot_delete_sync_ecommerce()
    {
        $orderStatus = OrderStatus::create([
            'name' => 'testing',
            'code' => 'testing',
            'order_active' => 0,
            'sync_ecommerce' => 1,
        ]);

        $response = $this->delete(route('api.settings.order-statuses.destroy', $orderStatus));
        $response->assertStatus(401);
    }
}
