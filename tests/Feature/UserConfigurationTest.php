<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserConfigurationTest extends TestCase
{
    public function test_successful_post()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $data = [];

        $response = $this->json('POST', 'api/user/configuration', $data);

        $response->assertStatus(200);
    }

    public function test_successful_get()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->json('GET', 'api/user/configuration');

        $response->assertStatus(200);
    }
}
