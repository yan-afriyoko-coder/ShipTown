<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\AutoStatus\ConfigurationController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_index_call_returns_ok()
    {
        $response = $this->get(route('modules.autostatus.picking.configuration.index'));

        $response->assertSuccessful();
    }
}
