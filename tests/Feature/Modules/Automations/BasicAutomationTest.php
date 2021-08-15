<?php

namespace Tests\Feature\Modules\Automations;

use App\Events\Order\OrderCreatedEvent;
use App\Models\Order;
use App\Modules\Automations\src\Executors\Order\SetStatusCodeExecutor;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Models\Execution;
use App\Modules\Automations\src\Validators\Order\StatusCodeEqualsValidator;
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
        AutoPackingWebServiceProvider::disableModule();

        /** @var Automation $automation */
        $automation = Automation::create([
            'name' => 'Paid to Picking',
            'event_class' => OrderCreatedEvent::class,
        ]);

        Condition::create([
            'automation_id' => $automation->getKey(),
            'validation_class' => StatusCodeEqualsValidator::class,
            'condition_value' => 'paid'
        ]);

        Execution::create([
            'automation_id' => $automation->getKey(),
            'execution_class' => SetStatusCodeExecutor::class,
            'execution_value' => 'new_status'
        ]);

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => 'paid']);

        OrderCreatedEvent::dispatch($order);

        $order = $order->refresh();

        $this->assertEquals('new_status', $order->status_code);
    }
}
