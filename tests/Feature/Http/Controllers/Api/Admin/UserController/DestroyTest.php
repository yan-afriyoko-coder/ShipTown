<?php

namespace Tests\Feature\Http\Controllers\Api\Admin\UserController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_destroy_call_returns_ok()
    {
        $user = User::factory()->create()->assignRole('user');

        $response = $this->delete('api/admin/users/'.$user->id);

        $response->assertOk();

        $this->assertDatabaseMissing('users', ['id' => $user->id, 'deleted_at' => null]);
    }
}
