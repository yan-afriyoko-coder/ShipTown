<?php

namespace Tests\Feature\Api;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class UsersTest extends TestCase
{
    public function testUserDestroyEndpoint()
    {
        Passport::actingAs(
            \factory(User::class)->states('admin')->create()
        );

        $user = factory(User::class)->create();

        $response = $this->delete("api/users/$user->id");

        $response->assertOk();
        $this->assertNull(User::find($user->id));
    }

    // Tests that only admins are able to delete other users
    public function testUserDestroyAsNonadminUser()
    {
        Passport::actingAs(
            \factory(User::class)->create()
        );

        $user = factory(User::class)->create();

        $response = $this->delete("api/users/$user->id");

        $response->assertForbidden();
        $this->assertNotNull(User::find($user->id));
    }
}
