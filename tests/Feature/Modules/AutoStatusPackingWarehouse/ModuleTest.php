<?php

namespace Tests\Feature\Modules\AutoStatusPackingWarehouse;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Models\Order;
use App\Modules\AutoStatusPackingWarehouse\src\Listeners\ActiveOrdersCheckEvent\SetStatusPackingWarehouseListener;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Tests\TestCase;

class ModuleTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_functionality()
    {
        Bus::fake();

        $listener = Mockery::spy(SetStatusPackingWarehouseListener::class);
        app()->instance(SetStatusPackingWarehouseListener::class, $listener);

        $order = factory(Order::class)->create(['is_active' => true]);

        ActiveOrderCheckEvent::dispatch($order);

        $listener->shouldHaveReceived('handle')->once();
    }
}
