<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\ConfigurationController;

use App\Models\Configuration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function test_bulkupdate_call_returns_ok()
    {
        Configuration::create([
            'business_name' => 'Some name',
        ]);

        $response = $this->post(route('api.settings.configurations.store'), [
            'business_name' => 'new name',
        ]);
        $response->assertOk();

        $config = Configuration::first();
        $this->assertEquals($config->business_name, 'new name');
    }
}
