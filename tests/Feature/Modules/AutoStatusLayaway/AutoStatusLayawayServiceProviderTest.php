<?php

namespace Tests\Feature\Modules\AutoStatusLayaway;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\AutoStatusLayaway\src\AutoStatusLayawayServiceProvider;
use App\Modules\AutoStatusLayaway\src\Listeners\OrderUpdatedEvent\SetLayawayStatusListener;
use App\Modules\AutoStatusPackingWeb\src\AutoPackingWebServiceProvider;
use App\Modules\AutoStatusPackingWeb\src\Jobs\SetPackingWebStatusJob;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
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

        $order = factory(Order::class)->create(['status_code' => 'paid', 'is_active' => true]);

        OrderUpdatedEvent::dispatch($order);

        /** @var Order $order */
        $order = $order->refresh();

        $this->assertEquals('layaway', $order->status_code);
    }
}
