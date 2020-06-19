<?php

namespace Tests\Feature;

use App\Jobs\Api2cart\DispatchImportOrdersJobs;
use App\Jobs\Rmsapi\ImportProductsJob;
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

        Bus::assertDispatched(DispatchImportOrdersJobs::class);
        Bus::assertDispatched(ImportProductsJob::class);
    }
}
