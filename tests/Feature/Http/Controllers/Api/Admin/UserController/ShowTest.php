<?php

namespace Tests\Feature\Http\Controllers\Api\Admin\UserController;

use App\User;
use Tests\TestCase;

class ShowTest extends TestCase
{
    /** @test */
    public function test_show_call_returns_ok()
    {
        $user = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('users.show', $user->id));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role_id',
                'printer_id',
            ],
        ]);
    }
}
