<?php

namespace Tests\Feature\Run;

use App\Jobs\Orders\RecalculateOrderProductLineCountJob;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaintenanceRouteTest extends TestCase
{
    public function testIfRouteIsAvailableAfterAugust()
    {
        $this->assertTrue(
            Carbon::today()
                ->isBefore(
                    Carbon::createFromDate(2020, 9, 10)
                )
        );
    }

    public function testGetRoute()
    {
        Bus::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/run/maintenance');

        Bus::assertDispatched(
            RecalculateOrderProductLineCountJob::class
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
        $response = $this->get('/run/maintenance');

        $response->assertStatus(302);
    }
}
