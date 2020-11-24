<?php

namespace Tests\Feature\Pdf;

use Tests\TestCase;

class OrderAddressLabelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfRouteIsProtected()
    {
        $response = $this->get('pdf/orders/123/address_label');

        $response->assertStatus(302);
    }
}
