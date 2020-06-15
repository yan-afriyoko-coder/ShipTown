<?php

namespace Tests\Unit\Jobs\Api2cart;

use App\Jobs\Api2cart\ProcessImportedOrdersJob;
use App\Models\Api2cartOrderImports;
use App\Models\Order;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class ProcessImportedOrdersJobTest
 * @package Tests\Unit\Jobs\Api2cart
 */
class ProcessImportedOrdersJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_processes_correctly() {
        // Test for clean data
        $this->assertEquals(0, \App\Models\Order::count());

        $orderImport = factory(Api2cartOrderImports::class)->create();

        $job = new ProcessImportedOrdersJob();
        $job->handle();

        $unprocessedOrdersExists = Api2cartOrderImports::query()
            ->whereNull('when_processed')
            ->exists();

        $order = Order::where([ 'order_number' => $orderImport['raw_import']['id'] ])->first();

        $this->assertFalse($unprocessedOrdersExists, 'Some orders still not processed');
        $this->assertNotNull($order, 'Order should be added to the database');

        // Check if the order_products were saved and the relationship is working;
        $this->assertNotEmpty($order->orderProducts);
        $this->assertEquals($order->getKey(), $order->orderProducts[0]->order->getKey());
        
        
        $this->assertEquals('Bag', $order->orderProducts[0]->product->name);
    }
}
