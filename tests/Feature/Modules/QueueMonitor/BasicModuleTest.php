<?php

namespace Tests\Feature\Modules\QueueMonitor;

use App\Modules\QueueMonitor\src\Jobs\SampleJob;
use App\Modules\QueueMonitor\src\QueueMonitorServiceProvider;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        ray()->showApp();
        ray()->showEvents();

        QueueMonitorServiceProvider::enableModule();

        SampleJob::dispatch();

        $this->assertDatabaseHas('modules_queue_monitor_jobs', [
            'job_class' => SampleJob::class,
        ]);

        $this->markAsRisky();
    }
}
