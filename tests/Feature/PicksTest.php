<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PicksTest extends TestCase
{
    public function testGetAuthenticated()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/picks');

        $response->assertStatus(200);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetUnauthenticated()
    {
        $response = $this->get('/api/picks');

        $response->assertStatus(302);
    }
}
