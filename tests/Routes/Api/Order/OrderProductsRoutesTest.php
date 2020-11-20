<?php

namespace Tests\Routes\Api;

use Tests\Routes\AuthenticatedRoutesTestCase;

class OrderProductsRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGet()
    {
        $response = $this->get('/api/order/products');

        $response->assertStatus(200);
    }
}
