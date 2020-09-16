<?php

namespace Tests\Unit\Maintenance;

use App\Jobs\Maintenance\RunPackingWarehouseRuleOnPickingJob;
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

        $job = new RunPackingWarehouseRuleOnPickingJob();
        $job->handle();
    }
}
