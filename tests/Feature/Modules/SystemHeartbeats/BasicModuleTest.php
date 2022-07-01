<?php

namespace Tests\Feature\Modules\SystemHeartbeats;

use App\Events\DailyEvent;
use App\Events\Every10minEvent;
use App\Events\HourlyEvent;
use App\Models\Heartbeat;
use App\Modules\SystemHeartbeats\src\Listeners\DailyEventListener;
use App\Modules\SystemHeartbeats\src\Listeners\Every10minEventListener;
use App\Modules\SystemHeartbeats\src\Listeners\HourlyEventListener;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_every10minEvent_heartbeat()
    {
        Heartbeat::query()->forceDelete();

        Every10minEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => Every10minEventListener::class
        ]);
    }

        /** @test */
    public function test_hourlyEvent_heartbeat()
    {
        Heartbeat::query()->forceDelete();

        HourlyEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => HourlyEventListener::class
        ]);
    }

    /** @test */
    public function test_dailyEvent_heartbeat()
    {
        Heartbeat::query()->forceDelete();

        DailyEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => DailyEventListener::class
        ]);
    }
}
