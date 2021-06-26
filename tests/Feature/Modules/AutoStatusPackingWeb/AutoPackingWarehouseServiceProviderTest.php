<?php

namespace Tests\Feature\Modules\AutoStatusPackingWeb;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\AutoStatusPackingWeb\src\AutoPackingWebServiceProvider;
use App\Modules\AutoStatusPackingWeb\src\Jobs\SetPackingWebStatusJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AutoPackingWarehouseServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_functionality()
    {
        AutoPackingWebServiceProvider::enableModule();

        $order = factory(Order::class)->create(['is_active' => true]);

        Bus::fake();

        OrderUpdatedEvent::dispatch($order);

        Bus::assertDispatched(SetPackingWebStatusJob::class);
    }
}
