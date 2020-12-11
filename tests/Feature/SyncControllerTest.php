<?php

namespace Tests\Feature;

use App\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\User;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SyncControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfDispatchesJobs()
    {
        Bus::fake();

        factory(RmsapiConnection::class)->create();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/run/sync');

        Bus::assertDispatched(DispatchImportOrdersJobs::class);
        Bus::assertDispatched(FetchUpdatedProductsJob::class);
    }
}
