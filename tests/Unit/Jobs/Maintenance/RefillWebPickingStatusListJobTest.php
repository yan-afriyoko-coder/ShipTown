<?php

namespace Tests\Unit\Jobs\Maintenance;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Modules\AutoStatus\src\Jobs\RefillStatusesJob;
use App\Services\AutoPilot;
use Tests\TestCase;

class RefillWebPickingStatusListJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Product::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();

        factory(Product::class, 30)->create();

        factory(Order::class, 150)
            ->with('orderProducts', 2)
            ->create(['status_code' => 'paid']);

        RefillStatusesJob::dispatchNow();

        $this->assertEquals(
            AutoPilot::getBatchSize(),
            Order::whereStatusCode('picking')->count()
        );
    }
}
