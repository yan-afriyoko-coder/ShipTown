<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Automation\AutomationController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $data = [
            'name' => 'Test Automation',
            'event_class' => 'App\Events\Order\OrderCreatedEvent',
            'enabled' => true,
            'prioriry' => 1,
            'conditions' => [
                [
                    'validation_class' => 'App\Modules\Automations\src\Validators\Order\StatusCodeEqualsValidator',
                    'condition_value' => 'paid'
                ],
                [
                    'validation_class' => 'App\Modules\Automations\src\Validators\Order\ShippingMethodCodeEqualsValidator',
                    'condition_value' => 'paid'
                ]
            ],
            'executions' => [
                [
                    'priority' => 1,
                    'execution_class' => 'App\Modules\Automations\src\Executors\Order\SetStatusCodeExecutor',
                    'execution_value' => 'store_pickup',
                ]
            ]
        ];
        $response = $this->post(route('api.settings.module.automations.store'), $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'enabled'
            ]
        ]);
    }
}
