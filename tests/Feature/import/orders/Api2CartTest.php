<?php

namespace Tests\Feature\import\orders;

use Illuminate\Support\Facades\Queue;
use Tests\Feature\AuthorizedUserTestCase;
use Tests\TestCase;

class api2CartTest extends TestCase
{
    use AuthorizedUserTestCase;

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
