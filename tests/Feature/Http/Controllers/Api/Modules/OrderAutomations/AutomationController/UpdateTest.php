<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\OrderAutomations\AutomationController;

use App\Modules\Automations\src\Models\Automation;
use App\User;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_update_call_returns_ok()
    {
        $automation = Automation::create([
            'name' => 'Store Pickup',
            'priority' => 1,
        ]);

        $data = [
            'name' => 'Test Automation',
            'enabled' => true,
            'description' => 'Some description',
            'priority' => 1,
            'conditions' => [
                [
                    'condition_class' => \App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationCondition::class,
                    'condition_value' => 'paid'
                ],
                [
                    'condition_class' => \App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsCondition::class,
                    'condition_value' => 'paid'
                ]
            ],
            'actions' => [
                [
                    'priority' => 1,
                    'action_class' => \App\Modules\Automations\src\Actions\Order\SetStatusCodeAction::class,
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
