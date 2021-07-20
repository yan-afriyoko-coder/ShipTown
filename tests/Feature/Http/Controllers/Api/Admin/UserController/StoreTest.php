<?php

namespace Tests\Feature\Http\Controllers\Api\Admin\UserController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $response = $this->post(route('users.store'), [
            'name'              => 'Test User',
            'email'              => 'testing@example.com',
            'role_id'      => Role::first()->id,
        ]);

        $response->assertStatus(201);
    }
}
