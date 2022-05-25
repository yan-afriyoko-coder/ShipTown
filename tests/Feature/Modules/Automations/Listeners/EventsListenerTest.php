<?php

namespace Tests\Feature\Modules\Automations\Listeners;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Modules\Automations\src\AutomationsServiceProvider;
use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class EventsListenerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_automations_are_fired_on_active_order_check_event()
    {
        Bus::fake();

        /** @var Order $order */
        $order = factory(Order::class)->create();

        ActiveOrderCheckEvent::dispatch($order);

        Bus::assertDispatched(RunAutomationsOnActiveOrdersJob::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_automations_are_fired_on_order_updated_event()
    {
        AutomationsServiceProvider::enableModule();

        Bus::fake(RunAutomationsOnActiveOrdersJob::class);

        /** @var OrderStatus $activeOrderStatus */
        $activeOrderStatus = factory(OrderStatus::class)->create(['order_active' => true]);

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => $activeOrderStatus->code]);

        $order->update(['status_code' => 'new_status']);

        Bus::assertDispatched(RunAutomationsOnActiveOrdersJob::class);
    }
}
