<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Api2cart\Api2cartConnectionController;

use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole(Role::findOrCreate('admin', 'api'));
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_destroy_call_returns_ok()
    {
        $api2cart = Api2cartConnection::factory()->create();
        $response = $this->delete(route('api.settings.module.api2cart.connections.destroy', $api2cart));
        $response->assertStatus(200);
    }
}
