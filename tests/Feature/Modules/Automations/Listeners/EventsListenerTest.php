<?php

namespace Tests\Feature\Modules\Automations\Listeners;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\Automations\src\AutomationsServiceProvider;
use App\Modules\Automations\src\Listeners\ActiveOrderCheckEventListener;
use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class EventsListenerTest extends TestCase
{
    use RefreshDatabase;

    private $listener;

    protected function setUp(): void
    {
        parent::setUp();

//        $this->listener = Mockery::mock(AutomationService::class);
//        app()->instance(AutomationService::class, $this->listener);

        AutomationsServiceProvider::enableModule();


        $this->listener = Mockery::mock(ActiveOrderCheckEventListener::class);
        app()->instance(ActiveOrderCheckEventListener::class, $this->listener);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_automations_are_fired_on_active_order_check_event()
    {
        $this->listener->shouldReceive('handle');

        /** @var Order $order */
        $order = factory(Order::class)->create();

        ActiveOrderCheckEvent::dispatch($order);

        $this->listener->shouldHaveReceived('handle', [ActiveOrderCheckEvent::class]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_automations_are_fired_on_order_updated_event()
    {
        $this->listener->shouldReceive('handle');

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => 'paid']);
        $order->status_code = 'new_status';
        $order->save();

        $this->listener->shouldHaveReceived('handle', [ActiveOrderCheckEvent::class]);
    }
}
