<?php

namespace Tests\Unit;

use App\Jobs\Api2cart\ProcessImportedOrdersJob;
use App\Models\Api2cartOrderImports;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckIfRawImportColumnExistsTest extends TestCase
{
    // raw_import column is required because LIVE.WEB.GUINEYS.RMSAPI.PRODUCTS.MANAGEMENT is using it
    public function test_if_raw_import_column_is_populated_on_api2cart_import() {
        // Test for clean data
        Order::query()->delete();
        $this->assertEquals(0, \App\Models\Order::count());

        $orderImport = factory(Api2cartOrderImports::class)->create();

        $job = new ProcessImportedOrdersJob();
        $job->handle();


        $order = Order::where([ 'order_number' => $orderImport['raw_import']['id'] ])->first();

        $this->assertNotNull($order, 'Order should be added to the database');

        $this->assertNotEmpty($order->raw_import);

    }
}
