<?php

namespace Tests\Routes\Api\Admin;

use Tests\Routes\AuthenticatedRoutesTestCase;

class UsersRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGet()
    {
        auth()->user()->assignRole('admin');

        $response = $this->get('/api/admin/users');

        $response->assertStatus(200);
    }
}
