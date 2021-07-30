<?php

namespace Tests\Feature\Http\Controllers\Api\OrderStatusController;

use App\Models\OrderStatus;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_index_call_returns_ok()
    {
        factory(OrderStatus::class)->create();

        $response = $this->get('api/order-statuses');

        $response->assertSuccessful();
    }
}
