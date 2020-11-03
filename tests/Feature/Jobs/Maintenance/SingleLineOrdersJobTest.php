<?php

namespace Tests\Feature\Jobs\Maintenance;

use App\Jobs\Refill\RefillSingleLineOrdersJob;
use App\Models\Order;
use Tests\TestCase;

class SingleLineOrdersJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfJobMovesOrders()
    {
        Order::query()->forceDelete();

        factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'paid']);

        RefillSingleLineOrdersJob::dispatchNow();

        $this->assertDatabaseMissing('orders', [
            'status_code' => 'paid',
            'product_line_count' => 1
        ]);
    }
}
