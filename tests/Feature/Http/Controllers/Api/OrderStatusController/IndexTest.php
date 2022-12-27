<?php

namespace Tests\Feature\Http\Controllers\Api\OrderStatusController;

use App\Models\OrderStatus;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_index_call_returns_ok()
    {
        OrderStatus::factory()->create();

        $response = $this->get('api/order-statuses');

        $response->assertSuccessful();
    }
}
