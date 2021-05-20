<?php

namespace Tests\Feature\Jobs\Maintenance;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Modules\Maintenance\src\Jobs\Products\RecalculateProductQuantityReservedJob;
use Tests\TestCase;

class RecalculateProductQuantityReservedJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Product::query()->forceDelete();
        Order::query()->forceDelete();
        OrderProduct::query()->forceDelete();

        factory(Order::class)
            ->with('orderProducts', 2)
            ->create(['status_code' => 'paid']);

        Product::query()->update([
            'quantity_reserved' => 0,
        ]);

        RecalculateProductQuantityReservedJob::dispatchNow();

        $this->markTestIncomplete();
    }
}
