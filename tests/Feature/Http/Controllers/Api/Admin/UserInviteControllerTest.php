<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\UserInvite;
use App\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Admin\UserInviteController
 */
class UserInviteControllerTest extends TestCase
{
    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $invite = factory(UserInvite::class)->make();
        $user = factory(User::class)->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->postJson(route('invites.store'), [
            'email' => $invite->email
        ]);

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'message'
        ]);
    }

    // test cases...
}
