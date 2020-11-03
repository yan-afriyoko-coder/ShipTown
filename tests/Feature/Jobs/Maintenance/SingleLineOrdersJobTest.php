<?php

namespace Tests\Feature\Jobs\Maintenance;

use App\Jobs\Maintenance\RefillSingleLineOrdersJob;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
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
