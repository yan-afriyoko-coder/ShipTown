<?php

namespace Tests\Feature\Modules\Automations\Jobs;

use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;
use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RunAutomationsOnActiveOrdersJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_second_job_is_not_dispatched()
    {
        Bus::fake([RunAutomationsOnActiveOrdersJob::class]);

        \romanzipp\QueueMonitor\Models\Monitor::query()->create([
            'job_id' => 'test',
            'name' => RunAutomationsOnActiveOrdersJob::class,
            'finished_at' => null,
        ]);

        ray(\romanzipp\QueueMonitor\Models\Monitor::all()->toArray());

        AutomationService::dispatchAutomationsOnActiveOrders();

        Bus::assertNotDispatched(RunAutomationsOnActiveOrdersJob::class);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_dispatches_job()
    {
        Bus::fake([RunAutomationsOnActiveOrdersJob::class]);

        AutomationService::dispatchAutomationsOnActiveOrders();

        Bus::assertDispatched(RunAutomationsOnActiveOrdersJob::class);
    }
}
