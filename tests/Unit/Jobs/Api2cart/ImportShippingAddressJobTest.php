<?php

namespace Tests\Unit\Jobs\Api2cart;

use App\Jobs\Api2cart\ImportShippingAddressJob;
use App\Models\Order;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
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
        $import = factory(Api2cartOrderImports::class)->create();
        $import = $import->refresh();

        $order = factory(Order::class)->create([
            'order_number' => $import->order_number,
            'shipping_address_id' => null,
        ]);

        ImportShippingAddressJob::dispatchNow($order->id);

        $order = $order->refresh();

        $this->assertNotNull($order->shipping_address_id);
    }
}
