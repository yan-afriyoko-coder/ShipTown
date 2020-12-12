<?php

namespace Tests\Routes\Api\Admin;

use App\Models\Invite;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserInvitesRoutesTest extends TestCase
{
    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        User::query()->forceDelete();

        $user = factory(User::class)->create();
        $user->assignRole('admin');
        Passport::actingAs($user);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $invite = factory(Invite::class)->make();

        $response = $this->post('/api/admin/user/invites', $invite->toArray());

        $response->assertStatus(201);
    }
}
