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
        $this->doesNotPerformAssertions();

        $job = new RunPackingWarehouseRuleOnPaidOrdersJob();
        $job->handle();
    }
}
