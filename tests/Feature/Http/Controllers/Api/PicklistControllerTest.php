<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\PicklistController
 */
class PicklistControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('picklist.index'));

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
