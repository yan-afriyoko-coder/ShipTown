<?php

namespace Tests\Feature\Modules\Api2cart;

use App\Events\SyncRequestedEvent;
use App\Models\RmsapiConnection;
use App\Modules\Api2cart\src\Api2cartServiceProvider;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Tests\TestCase;

class SyncControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfDispatchesJobs()
    {
        Api2cartServiceProvider::enableModule();

        Bus::fake();

        SyncRequestedEvent::dispatch();

        Bus::assertDispatched(DispatchImportOrdersJobs::class);
    }
}
