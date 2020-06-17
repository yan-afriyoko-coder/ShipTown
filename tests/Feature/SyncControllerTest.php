<?php

namespace Tests\Feature;

use App\Jobs\Api2cart\ImportOrdersJob;
use App\Jobs\Rmsapi\SyncRmsapiConnectionCollectionJob;
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
    public function test_if_dispatches_jobs()
    {
        Bus::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/sync');

        Bus::assertDispatched(ImportOrdersJob::class);
        Bus::assertDispatched(SyncRmsapiConnectionCollectionJob::class);
    }
}
