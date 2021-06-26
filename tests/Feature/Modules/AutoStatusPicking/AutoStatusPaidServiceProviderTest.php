<?php

namespace Tests\Feature\Modules\AutoStatusPicking;

use App\Events\HourlyEvent;
use App\Modules\AutoStatusPicking\src\AutoStatusPickingServiceProvider;
use App\Modules\AutoStatusPicking\src\Jobs\RefillPickingIfEmptyJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AutoStatusPaidServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_functionality()
    {
        AutoStatusPickingServiceProvider::enableModule();

        Bus::fake();

        HourlyEvent::dispatch();

        Bus::assertDispatched(RefillPickingIfEmptyJob::class);
    }
}
