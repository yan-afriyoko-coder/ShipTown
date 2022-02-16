<?php

namespace Tests\Feature\Routes\Api;

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;
use App\User;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;

class SyncControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfDispatchesJobs()
    {
        User::query()->forceDelete();
        Bus::fake();

        factory(RmsapiConnection::class)->create();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/run/sync');

        Bus::assertDispatched(FetchUpdatedProductsJob::class);
    }
}
