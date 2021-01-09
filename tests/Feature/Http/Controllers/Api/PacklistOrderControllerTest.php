<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\PacklistOrderController
 */
class PacklistOrderControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        factory(Order::class)->create(['status_code' => 'packing']);

        $response = $this->actingAs($user, 'api')->getJson(route('order.index'));

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

    // test cases...
}
