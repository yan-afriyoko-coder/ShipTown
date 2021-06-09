<?php

namespace Tests\Feature\Jobs\Maintenance;

use App\Events\HourlyEvent;
use App\Models\Order;
use App\Modules\AutoStatusSingleLineOrders\src\Jobs\RefillSingleLineOrdersJob;
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

        HourlyEvent::dispatch();

        $this->assertDatabaseHas('orders', [
            'status_code' => 'single_line_orders',
            'product_line_count' => 1,
        ]);
    }
}
