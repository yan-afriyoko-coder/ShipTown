<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Admin\UserRoleController
 */
class UserRoleControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('admin.users.roles.index'));

        $response->assertOk();

//        $this->assertNotEquals(0, $response->json('meta.total'));

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'guard_name'
                ]
            ]
        ]);
    }

    // test cases...
}
