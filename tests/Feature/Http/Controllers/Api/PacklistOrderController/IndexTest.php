<?php

namespace Tests\Feature\Http\Controllers\Api\PacklistOrderController;

use App\Models\Order;
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
