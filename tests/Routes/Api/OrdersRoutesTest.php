<?php

namespace Tests\Routes\Api;

use Tests\Routes\AuthenticatedRoutesTestCase;

class OrdersRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGet()
    {
        $response = $this->get('/api/orders');

        $response->assertStatus(200);
    }
}
