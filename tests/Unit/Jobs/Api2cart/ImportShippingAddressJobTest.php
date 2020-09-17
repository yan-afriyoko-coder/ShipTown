<?php

namespace Tests\Unit\Jobs\Api2cart;

use App\Jobs\Api2cart\ImportShippingAddressJob;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportShippingAddressJobTest extends TestCase
{
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

        $order = Order::where(['order_number' => $import->order_number])->first();
        $order->update(['shipping_address_id' => null]);

        ImportShippingAddressJob::dispatchNow($order->id);

        $order = $order->refresh();

        $this->assertNotNull($order->shipping_address_id);
    }
}
