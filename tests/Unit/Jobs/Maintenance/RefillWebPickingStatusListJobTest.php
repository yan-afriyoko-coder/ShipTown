<?php

namespace Tests\Unit\Jobs\Maintenance;

use App\Jobs\Maintenance\RefillWebPickingStatusListJob;
use App\Models\Order;
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

        factory(Order::class, 150)->create(['status_code' => 'paid']);
        factory(Order::class, rand(20, 50))->create(['status_code' => 'picking']);

        RefillWebPickingStatusListJob::dispatch();

        $this->assertEquals(150, Order::whereStatusCode('picking')->count());
    }
}
