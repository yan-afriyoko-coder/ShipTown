<?php

namespace Tests\Feature\Modules\SystemHeartbeats;

use App\Events\DailyEvent;
use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryHourEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
use App\Models\Heartbeat;
use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsEventServiceProviderBase;
use App\Modules\SystemHeartbeats\src\Listeners\DailyEventListener;
use App\Modules\SystemHeartbeats\src\Listeners\EveryFiveMinutesEventListener;
use App\Modules\SystemHeartbeats\src\Listeners\EveryMinuteEventListener;
use App\Modules\SystemHeartbeats\src\Listeners\EveryTenMinutesEventListener;
use App\Modules\SystemHeartbeats\src\Listeners\HourlyEventListener;
use App\Modules\SystemHeartbeats\src\SystemHeartbeatsServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        SystemHeartbeatsServiceProvider::enableModule();
        InventoryReservationsEventServiceProviderBase::enableModule();

        Heartbeat::query()->forceDelete();
    }

    /** @test */
    public function test_EveryMinuteEvent_heartbeat()
    {
        EveryMinuteEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => EveryMinuteEventListener::class
        ]);
    }

    /** @test */
    public function test_FiveMinutesEvent_heartbeat()
    {
        EveryFiveMinutesEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => EveryFiveMinutesEventListener::class
        ]);
    }

    /** @test */
    public function test_TenMinutesEvent_heartbeat()
    {
        EveryTenMinutesEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => EveryTenMinutesEventListener::class
        ]);
    }

        /** @test */
    public function test_hourlyEvent_heartbeat()
    {
        EveryHourEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => HourlyEventListener::class
        ]);
    }

    /** @test */
    public function test_dailyEvent_heartbeat()
    {
        DailyEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => DailyEventListener::class
        ]);
    }
}
