<?php

namespace Tests\Feature\Modules\Automations\Validators;

use App\Events\Order\OrderCreatedEvent;
use App\Models\Order;
use App\Modules\Automations\src\Executors\ChangeOrderStatusToExecutor;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Models\Execution;
use App\Modules\Automations\src\Validators\OrderShippingMethodCodeEqualsValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShippingMethodCodeEqualsValidatorTest extends TestCase
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
            'name' => 'Paid to Picking',
            'event_class' => OrderCreatedEvent::class,
        ]);

        /** @var Condition $condition */
        Condition::create([
            'automation_id' => $automation->getKey(),
            'validation_class' => OrderShippingMethodCodeEqualsValidator::class,
            'condition_value' => 'store_pickup'
        ]);

        Execution::create([
            'automation_id' => $automation->getKey(),
            'execution_class' => ChangeOrderStatusToExecutor::class,
            'execution_value' => 'store_pickup'
        ]);

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => 'paid', 'shipping_method_code' => 'store_pickup']);

        OrderCreatedEvent::dispatch($order);

        $order = $order->refresh();

        $this->assertEquals('store_pickup', $order->status_code);
    }
}
