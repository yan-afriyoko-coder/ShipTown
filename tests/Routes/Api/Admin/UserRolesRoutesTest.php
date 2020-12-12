<?php

namespace Tests\Routes\Api\Admin;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserRolesRoutesTest extends TestCase
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
        $response = $this->get('/api/admin/user/roles');

        $response->assertStatus(200);
    }
}
