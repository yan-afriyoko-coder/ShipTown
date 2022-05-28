<?php

namespace Tests\Feature\Modules\Automations;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Models\Order;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\AutomationsServiceProvider;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsOrderConditionAbstract;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_automation()
    {
        AutomationsServiceProvider::enableModule();

        /** @var Automation $automation */
        $automation = Automation::create([
            'enabled' => false,
            'name' => 'Paid to Picking',
            'event_class' => ActiveOrderCheckEvent::class,
        ]);

        Condition::create([
            'automation_id' => $automation->getKey(),
            'condition_class' => StatusCodeEqualsOrderConditionAbstract::class,
            'condition_value' => 'paid'
        ]);

        Action::create([
            'automation_id' => $automation->getKey(),
            'action_class' => SetStatusCodeAction::class,
            'action_value' => 'new_status'
        ]);

        $automation->enabled = true;
        $automation->save();

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => 'paid']);

        ActiveOrderCheckEvent::dispatch($order);

        $order = $order->refresh();

        $this->assertEquals('new_status', $order->status_code);
    }
}
