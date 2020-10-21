<?php

namespace Tests\Unit\Jobs\Maintenance;

use App\Jobs\Maintenance\RunPackingWarehouseRuleOnPaidOrdersJob;
use Tests\TestCase;

class RunPackingWarehouseRuleOnProcessingJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfRunsWithoutExceptions()
    {
        $job = new RunPackingWarehouseRuleOnPaidOrdersJob();
        $job->handle();

        // we just want to make sure it does not throw any errors
        $this->assertTrue(true);
    }
}
