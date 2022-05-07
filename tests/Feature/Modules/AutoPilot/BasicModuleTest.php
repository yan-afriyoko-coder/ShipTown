<?php

namespace Tests\Feature\Modules\AutoPilot;

use App\Events\HourlyEvent;
use App\Modules\AutoPilot\src\AutoPilotServiceProvider;
use App\Modules\AutoPilot\src\Jobs\ClearPackerIdJob;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_dispatches_jobs()
    {
        AutoPilotServiceProvider::enableModule();

        Bus::fake();

        HourlyEvent::dispatch();

        Bus::assertDispatched(ClearPackerIdJob::class);
    }
}
