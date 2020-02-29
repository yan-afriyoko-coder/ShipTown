<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryRoutesTest extends TestCase
{
    public function test_get_route_unauthorized()
    {
        $response = $this->get('/api/inventory');

        $response->assertStatus(302);
    }

    public function test_get_route_authorized()
    {
        Passport::actingAs(factory(User::class)->make());

        $response = $this->get('/api/inventory');

        $response->assertStatus(200);
    }

}
