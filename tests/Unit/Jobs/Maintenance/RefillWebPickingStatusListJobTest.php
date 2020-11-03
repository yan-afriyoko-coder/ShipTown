<?php

namespace Tests\Unit\Jobs\Maintenance;

use App\Jobs\RefillStatusesJob;
use App\Models\Order;
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
        Order::query()->forceDelete();

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
