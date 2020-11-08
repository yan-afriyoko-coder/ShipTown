<?php

namespace Tests\Feature\Jobs;

use App\Jobs\Refill\RefillOldOrdersToPickingJob;
use App\Models\Order;
use Carbon\Carbon;
use Tests\TestCase;

class RefillOldOrdertoPickingJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Order::query()->forceDelete();

        $order = factory(Order::class)
            ->create([
                'status_code' => 'paid',
                'order_placed_at' => Carbon::now()->subDays(7),
            ]);

        RefillOldOrdersToPickingJob::dispatchNow();

        $this->assertDatabaseHas('orders', ['status_code' => 'picking']);
    }
}
