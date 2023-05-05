<?php

namespace Tests\Feature\Modules\Telescope;

use App\Events\DailyEvent;
use App\Events\HourlyEvent;
use App\Modules\Telescope\src\Jobs\PruneEntriesJob;
use App\Modules\Telescope\src\TelescopeModuleServiceProvider;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function test_module_basic_functionality()
    {
        TelescopeModuleServiceProvider::enableModule();

        Bus::fake();

        HourlyEvent::dispatch();

        Bus::assertDispatched(PruneEntriesJob::class);
    }

    public function test_PruneEntriesJob()
    {
        TelescopeModuleServiceProvider::enableModule();

        PruneEntriesJob::dispatch();

        $this->assertDatabaseCount('telescope_entries', 0);
    }
}
