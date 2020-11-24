<?php

namespace Tests\Routes\Api\Product;

use Tests\Routes\AuthenticatedRoutesTestCase;

class ProductInventoryRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGet()
    {
        $response = $this->get('/api/product/inventory');

        $response->assertStatus(200);
    }
}
