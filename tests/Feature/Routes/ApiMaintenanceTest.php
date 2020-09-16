<?php

namespace Tests\Feature\Routes;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ApiMaintenanceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/run/maintenance');

        $response->assertStatus(200);
    }
}
