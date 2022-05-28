<?php

namespace Tests\Feature\Modules\Automations\Conditions;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Models\Order;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\AutomationsServiceProvider;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsOrderCondition;
use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShippingMethodCodeEqualsConditionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_shipping_method_code_equals_validator()
    {
        AutomationsServiceProvider::enableModule();

        /** @var Automation $automation */
        $automation = Automation::create([
            'enabled' => true,
            'name' => 'paid to store_pickup',
            'event_class' => ActiveOrderCheckEvent::class,
        ]);

        /** @var Condition $condition */
        Condition::create([
            'automation_id' => $automation->getKey(),
            'condition_class' => StatusCodeEqualsCondition::class,
            'condition_value' => 'paid'
        ]);

        /** @var Condition $condition */
        Condition::create([
            'automation_id' => $automation->getKey(),
            'condition_class' => ShippingMethodCodeEqualsOrderCondition::class,
            'condition_value' => 'store_pickup'
        ]);

        Action::create([
            'automation_id' => $automation->getKey(),
            'action_class' => SetStatusCodeAction::class,
            'action_value' => 'store_pickup'
        ]);

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => 'paid', 'shipping_method_code' => 'store_pickup']);

        ActiveOrderCheckEvent::dispatch($order);

        $order = $order->refresh();

        $this->assertEquals('store_pickup', $order->status_code);
    }
}
