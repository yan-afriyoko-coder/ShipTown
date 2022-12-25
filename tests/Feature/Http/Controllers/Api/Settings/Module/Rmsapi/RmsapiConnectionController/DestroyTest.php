<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Rmsapi\RmsapiConnectionController;

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
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
        $rmsApi = RmsapiConnection::factory()->create();

        $response = $this->delete(route('api.settings.module.rmsapi.connections.destroy', $rmsApi));
        $response->assertStatus(200);
    }
}
