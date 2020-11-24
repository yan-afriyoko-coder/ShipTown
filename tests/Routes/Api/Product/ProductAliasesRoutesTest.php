<?php

namespace Tests\Routes\Api\Product;

use Tests\Routes\AuthenticatedRoutesTestCase;

class ProductAliasesRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGet()
    {
        $response = $this->get('/api/product/aliases');

        $response->assertStatus(200);
    }
}
