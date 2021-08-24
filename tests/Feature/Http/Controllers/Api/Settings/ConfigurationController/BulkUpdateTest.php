<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\ConfigurationController;

use App\Models\Configuration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_bulkupdate_call_returns_ok()
    {
        Configuration::create(['key' => 'test', 'value' => 'value']);

        $response = $this->put(route('api.settings.configurations.bulk-update'), [
            'configs' => [
                'test' => 'new value',
                'not_exist' => 'value'
            ]
        ]);
        $response->assertOk();

        $config = Configuration::where('key', 'test')->first();
        $this->assertEquals($config->value, 'new value');
    }
}
