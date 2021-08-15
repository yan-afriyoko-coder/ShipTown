<?php

namespace Tests\Feature\Modules\Automations;

use App\Events\Order\OrderCreatedEvent;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\Automations\src\Executors\ChangeOrderStatusToExecutor;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Models\Execution;
use App\Modules\Automations\src\Services\AutomationService;
use App\Modules\Automations\src\Validators\OrderStatusIsValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Tests\TestCase;

class basicModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function basic_test()
    {
        /** @var Automation $automation */
        $automation = Automation::create([
            'name' => 'Paid to Picking',
            'event_class' => OrderCreatedEvent::class,
        ]);

        Condition::create([
            'automation_id' => $automation->getKey(),
            'validation_class' => OrderStatusIsValidator::class,
            'condition_value' => 'paid'
        ]);

        Execution::create([
            'automation_id' => $automation->getKey(),
            'execution_class' => ChangeOrderStatusToExecutor::class,
            'execution_value' => 'picking'
        ]);

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => 'paid']);

        OrderCreatedEvent::dispatch($order);

        $order = $order->refresh();

        $this->assertEquals('picking', $order->status_code);
    }
}
