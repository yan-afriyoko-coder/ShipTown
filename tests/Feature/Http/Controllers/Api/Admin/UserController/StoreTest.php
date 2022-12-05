<?php

namespace Tests\Feature\Http\Controllers\Api\Admin\UserController;

use App\Models\Warehouse;
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
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->post(route('users.store'), [
            'name'          => 'Test User',
            'email'         => 'testing@example.com',
            'role_id'       => Role::first()->id,
            'warehouse_id'  => $warehouse->id
        ]);

        $response->assertStatus(201);
    }

    public function test_add_deleted_user_return_ok()
    {
        $user = User::factory()->create()->assignRole('user');
        $user->delete();

        $response = $this->post(route('users.store'), [
            'name'      => $user->name,
            'email'     => $user->email,
            'role_id'   => Role::first()->id,
        ]);

        $response->assertStatus(200);
    }
}
