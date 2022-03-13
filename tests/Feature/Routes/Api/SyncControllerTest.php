<?php

namespace Tests\Feature\Routes\Api;

use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\RmsapiModuleServiceProvider;
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
        Bus::fake();

        RmsapiModuleServiceProvider::enableModule();

        factory(RmsapiConnection::class)->create();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/run/sync');

        Bus::assertDispatched(FetchUpdatedProductsJob::class);
    }
}
