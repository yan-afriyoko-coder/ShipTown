<?php

namespace Tests\Routes\Api\Run;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DailyJobsRoutesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/run/daily/jobs');

        $response->assertStatus(200);
    }
}
