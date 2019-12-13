<?php

namespace Tests\Feature\web;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProtectedRoutesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_products_route_is_not_accesible_when_not_logged_in()
    {
        $response = $this->get('/products');

        $response->assertStatus(302);
    }
}
