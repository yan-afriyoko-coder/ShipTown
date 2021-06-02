<?php

namespace Tests\Feature\Jobs;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\AutoStatusPicking\src\Jobs\RefillOldOrdersToPickingJob;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefillOldOrdersPickingJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $order = factory(Order::class)->create([
            'order_placed_at' => Carbon::now()->subDays(100),
            'status_code' => 'processing'
        ]);

        factory(OrderProduct::class)->create([
            'order_id' => $order->id
        ]);

        $order->update(['status_code' => 'paid']);

        RefillOldOrdersToPickingJob::dispatchNow();

        $this->assertDatabaseHas('orders', ['status_code' => 'picking']);
    }
}
