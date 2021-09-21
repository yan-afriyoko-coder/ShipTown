<?php

namespace Tests\Feature\Modules\Automations;

use App\Events\Order\OrderCreatedEvent;
use App\Models\Order;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationCondition;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Conditions\Order\CanBeFulfilledCondition;
use App\Modules\AutoStatusPackingWeb\src\AutoPackingWebServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicAutomationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_automation()
    {
        /** @var Automation $automation */
        $automation = Automation::create([
            'enabled' => true,
            'name' => 'Paid to Picking',
            'event_class' => OrderCreatedEvent::class,
        ]);

        Condition::create([
            'automation_id' => $automation->getKey(),
            'condition_class' => CanFulfillFromLocationCondition::class,
            'condition_value' => 'paid'
        ]);

        Action::create([
            'automation_id' => $automation->getKey(),
            'action_class' => SetStatusCodeAction::class,
            'action_value' => 'new_status'
        ]);

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => 'paid']);

        OrderCreatedEvent::dispatch($order);

        $order = $order->refresh();

        $this->assertEquals('new_status', $order->status_code);
    }
}
