<?php

namespace Tests\Unit\Jobs\Maintenance;

use App\Jobs\Maintenance\Archive\UpdateTotalAndTotalPaid;
use App\Models\Order;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Tests\TestCase;

class UpdtetotalAndTotalPaidJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfUpdatesTotals()
    {
        Order::query()->forceDelete();
        Api2cartOrderImports::query()->forceDelete();

        factory(Api2cartOrderImports::class)->create();

        Order::query()->update(['total' => 0, 'total_paid' => 0]);

        UpdateTotalAndTotalPaid::dispatch();

        $this->assertDatabaseMissing('orders', ['total' => 0]);
        $this->assertDatabaseMissing('orders', ['total_paid' => 0]);
    }
}
