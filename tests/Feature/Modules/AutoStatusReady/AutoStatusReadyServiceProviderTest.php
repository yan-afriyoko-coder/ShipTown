<?php

namespace Tests\Feature\Modules\AutoStatusReady;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\AutoStatusReady\src\AutoStatusReadyServiceProvider;
use App\Modules\AutoStatusReady\src\Jobs\SetReadyStatusWhenPackedJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AutoStatusReadyServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_functionality()
    {
        AutoStatusReadyServiceProvider::enableModule();

        /** @var Order $order */
        $order = factory(Order::class)->create();

        $order->is_packed = true;
        $order->save();

        $order = $order->refresh();

        $this->assertEquals('complete', $order->status_code);
    }
}
