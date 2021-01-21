<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Order;
use App\User;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\LogController
 */
class LogControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Activity::query()->forceDelete();

        $order = factory(Order::class)->create();

        $order->update(['status_code' => 'something_random_52']);

        $response = $this->get('api/logs?subject_type=App\Models\Order');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'created_at',
                    'id',
                    'description',
                    'subject_id',
                    'subject_type',
                    'causer_id',
                    'causer_type',
                    'properties' => [
                        '*' => []
                    ],
                    'changes' => []
                ]
            ],
            'meta',
        ]);
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('logs.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                ]
            ]
        ]);
    }

    // test cases...
}
