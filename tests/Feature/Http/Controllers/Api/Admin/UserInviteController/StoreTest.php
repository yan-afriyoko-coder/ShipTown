<?php

namespace Tests\Feature\Http\Controllers\Api\Admin\UserInviteController;

use App\Models\UserInvite;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        $invite = factory(UserInvite::class)->make();
        $user = factory(User::class)->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->postJson(route('invites.store'), [
            'email' => $invite->email,
        ]);

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'message',
        ]);
    }
}
