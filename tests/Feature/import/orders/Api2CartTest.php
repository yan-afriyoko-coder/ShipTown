<?php

namespace Tests\Feature\import\orders;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class api2CartTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_route()
    {
        $response = $this->get('api/import/orders/api2cart');

        $response->assertStatus(200);
    }
}
