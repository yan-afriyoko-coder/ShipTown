<?php

namespace Tests\Feature\Run;

use App\Jobs\Maintenance\RecalculateOrderProductLineCountJob;
use App\Jobs\RunMaintenanceJobs;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Tests\TestCase;

class MaintenanceRouteTest extends TestCase
{
    public function testGetRoute()
    {
        Bus::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/run/maintenance');

        Bus::assertDispatched(
            RunMaintenanceJobs::class
        );

        $response->assertStatus(200);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetRouteUnauthenticated()
    {
        $response = $this->get('/api/run/maintenance');

        $response->assertStatus(302);
    }
}
