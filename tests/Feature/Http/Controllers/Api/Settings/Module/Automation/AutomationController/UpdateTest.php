<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Automation\AutomationController;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\Automations\src\Models\Automation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_update_call_returns_ok()
    {
        $automation = Automation::create([
            'name' => 'Store Pickup',
            'priority' => 1,
            'event_class' => ActiveOrderCheckEvent::class,
        ]);

        $data = [
            'name' => 'Test Automation',
            'event_class' => ActiveOrderCheckEvent::class,
            'enabled' => true,
            'description' => 'Some description',
            'priority' => 1,
            'conditions' => [
                [
                    'condition_class' => 'App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationOrderCondition',
                    'condition_value' => 'paid'
                ],
                [
                    'condition_class' => 'App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsOrderCondition',
                    'condition_value' => 'paid'
                ]
            ],
            'actions' => [
                [
                    'priority' => 1,
                    'action_class' => 'App\Modules\Automations\src\Actions\Order\SetStatusCodeAction',
                    'action_value' => 'store_pickup',
                ]
            ]
        ];
        $response = $this->put(route('api.settings.module.automations.update', $automation->id), $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'enabled'
            ]
        ]);
    }
}
