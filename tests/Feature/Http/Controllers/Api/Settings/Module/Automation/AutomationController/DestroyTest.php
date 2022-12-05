<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Automation\AutomationController;

use App\Events\Order\OrderCreatedEvent;
use App\Modules\Automations\src\Models\Automation;
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
        $automation = Automation::create([
            'name' => 'Store Pickup',
            'priority' => 1,
        ]);

        $response = $this->delete(route('api.settings.module.automations.destroy', $automation));
        $response->assertOk();
    }
}
