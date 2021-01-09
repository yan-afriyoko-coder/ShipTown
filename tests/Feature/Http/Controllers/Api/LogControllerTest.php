<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\User;
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
