<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProtectedApiRoutesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_inventory_route_is_not_accesible_when_not_logged_in()
    {
        $response = $this->get('api/inventory');

        $response->assertStatus(302);
    }
}
