<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportRoutesTest extends TestCase
{
    public function test_if_import_from_api2cart_route_works()
    {
        $response = $this->get('import/orders/from/api2cart');

        $response->assertOk();
    }
}
