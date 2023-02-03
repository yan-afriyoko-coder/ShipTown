<?php

namespace Tests\Feature\Modules\QueueMonitor;

use App\Modules\QueueMonitor\src\Jobs\SampleJob;
use App\Modules\QueueMonitor\src\QueueMonitorServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        QueueMonitorServiceProvider::enableModule();

        SampleJob::dispatch();

        $this->assertDatabaseHas('modules_queue_monitor_jobs', [
            'job_class' => SampleJob::class,
        ]);

        $this->markAsRisky();
    }
}
