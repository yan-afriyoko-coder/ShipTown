<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Modules\RunAutomationController;

use App\Modules\Automations\src\Models\Automation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('user');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $automation = Automation::factory()->create();

        $response = $this->post(route('settings.modules.automations.run'),[
            'automation_id' => $automation->getKey()
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'automation_id',
                'time',
            ],
        ]);
    }
}
