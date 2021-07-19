<?php

namespace Tests\Feature\Modules\AutoStatusLayaway;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\AutoStatusLayaway\src\AutoStatusLayawayServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoStatusLayawayServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_functionality()
    {
        AutoStatusLayawayServiceProvider::enableModule();

        /** @var Order $order */
        $order = factory(Order::class)->make(['status_code' => 'paid', 'is_active' => true]);
        $order->save();

        //        $order->update(['status_code' => 'paid']);

        OrderUpdatedEvent::dispatch($order);

        /** @var Order $order */
        $order = $order->refresh();


        $this->assertEquals('layaway', $order->status_code);
    }
}
