<?php

namespace Tests\Unit\Jobs\Api2cart;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\Api2cart\src\Jobs\ProcessApi2cartImportedOrderJob;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProcessApi2cartImportedOrderJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Order::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        Api2cartOrderImports::query()->forceDelete();

        $importedOrder = factory(Api2cartOrderImports::class)->create();

        // act
        ProcessApi2cartImportedOrderJob::dispatch($importedOrder);

        // test
        $order = Order::query()->first();
        $importedOrder = $importedOrder->refresh();

        $this->assertNotNull($importedOrder->order_number, 'Order not populated on order_imports table');

        $this->assertNotNull($order, 'Order does not exist in database');
        $this->assertNotNull($order->status_code, 'Status code missing');
        $this->assertNotEquals(0, $order->total, 'Order total missing');
        $this->assertNotEquals(0, $order->total_paid, 'Order total_paid missing');
        $this->assertNotNull($order->shipping_address_id, 'Shipping address missing');
    }
}
