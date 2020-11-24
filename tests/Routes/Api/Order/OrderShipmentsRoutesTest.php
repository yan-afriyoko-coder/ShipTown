<?php

namespace Tests\Routes\Api\Order;

use Tests\Routes\AuthenticatedRoutesTestCase;

class OrderShipmentsRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGet()
    {
        $response = $this->get('/api/order/shipments');

        $response->assertStatus(200);
    }
}
