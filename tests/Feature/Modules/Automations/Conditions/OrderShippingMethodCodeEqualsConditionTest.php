<?php

namespace Tests\Feature\Modules\Automations\Conditions;

use App\Events\Order\OrderCreatedEvent;
use App\Models\Order;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsCondition;
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
        /** @var Automation $automation */
        $automation = Automation::create([
            'enabled' => true,
            'name' => 'Paid to Picking',
            'event_class' => OrderCreatedEvent::class,
        ]);

        /** @var Condition $condition */
        Condition::create([
            'automation_id' => $automation->getKey(),
            'condition_class' => ShippingMethodCodeEqualsCondition::class,
            'condition_value' => 'store_pickup'
        ]);

        Action::create([
            'automation_id' => $automation->getKey(),
            'action_class' => SetStatusCodeAction::class,
            'action_value' => 'store_pickup'
        ]);

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => 'paid', 'shipping_method_code' => 'store_pickup']);

        OrderCreatedEvent::dispatch($order);

        $order = $order->refresh();

        $this->assertEquals('store_pickup', $order->status_code);
    }
}
