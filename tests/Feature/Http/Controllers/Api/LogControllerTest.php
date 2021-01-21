<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Product;
use App\User;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\LogController
 */
class LogControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        Activity::query()->forceDelete();

        $user = factory(User::class)->create();

        // product creation will generate some logs
        factory(Product::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('logs.index'));

        $response->assertOk();

        $this->assertNotEquals(0, $response->json('meta.total'));

        $response->assertJsonStructure([
            'meta',
            'links',
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
            ]
        ]);
    }

    // test cases...
}
