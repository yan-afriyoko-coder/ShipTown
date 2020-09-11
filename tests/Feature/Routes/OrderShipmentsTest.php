<?php

namespace Tests\Feature\Routes;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderShipmentsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUnauthenticated()
    {
        $response = $this->get('/api/order/shipments');

        $response->assertStatus(302);
    }
}
