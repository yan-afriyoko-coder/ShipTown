<?php

namespace Tests\Feature\Http\Controllers\Api\Admin\UserController;

use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('admin', 'api'));

        $response = $this->actingAs($user, 'api')->getJson(route('users.index'));

        $response->assertOk();

        $this->assertGreaterThan(0, $response->json('meta.total'));

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'role_id',
                    'printer_id',
                ],
            ],
        ]);
    }
}
