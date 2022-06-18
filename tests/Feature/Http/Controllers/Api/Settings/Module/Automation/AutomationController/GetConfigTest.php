<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Automation\AutomationController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetConfigTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_get_config_call_returns_ok()
    {
        $response = $this->get(route('api.settings.module.automations.config'));

        $response->assertOk();
        $response->assertJsonStructure([
            'description',
            'conditions' => [
                '*' => [
                    'class',
                    'description'
                ]
            ],
            'actions' => [
                '*' => [
                    'class',
                    'description'
                ]
            ]
        ]);
    }
}
