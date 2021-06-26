<?php

namespace Tests\Feature\Modules\AutoStatusPackingWarehouse;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Models\Order;
use App\Modules\AutoStatusPackingWarehouse\src\AutoPackingWarehouseServiceProvider;
use App\Modules\AutoStatusPackingWarehouse\src\Jobs\SetStatusPackingWarehouseJob;
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
        AutoPackingWarehouseServiceProvider::enableModule();

        $order = factory(Order::class)->create(['is_active' => true]);

        Bus::fake();

        ActiveOrderCheckEvent::dispatch($order);

        Bus::assertDispatched(SetStatusPackingWarehouseJob::class);
    }
}
