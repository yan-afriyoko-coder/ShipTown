<?php

namespace Tests\Feature\Http\Controllers\Api\Settings;

use App\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Settings\UserMeController
 */
class UserMeControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('me.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ]
            ]
        ]);
    }
}
