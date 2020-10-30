<?php

namespace Tests\Unit\Jobs\Maintenance;

use App\Jobs\Maintenance\RefillPickingJob;
use App\Models\Order;
use App\Services\AutoPilot;
use App\User;
use Laravel\Passport\Passport;
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
//        factory(Order::class, rand(20, 50))->create(['status_code' => 'picking']);

        RefillPickingJob::dispatch();

        $this->assertEquals(
            AutoPilot::getBatchSize(),
            Order::whereStatusCode('picking')->count()
        );
    }
}
