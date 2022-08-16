<?php

namespace Tests\Unit\Jobs\Api2cart;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Modules\Api2cart\src\Jobs\ImportShippingAddressJob;
use App\Modules\Api2cart\src\Jobs\ProcessImportedOrdersJob;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportShippingAddressJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Order::query()->forceDelete();
        OrderAddress::query()->forceDelete();
        Api2cartOrderImports::query()->forceDelete();

        $import = factory(Api2cartOrderImports::class)->create();
        $import = $import->refresh();

        ProcessImportedOrdersJob::dispatch();

        $order = Order::where(['order_number' => $import->order_number])->first();
        $order->update(['shipping_address_id' => null]);

        ImportShippingAddressJob::dispatchNow($order->id);

        $order = $order->refresh();

        $this->assertNotNull($order->shipping_address_id);
    }
}
