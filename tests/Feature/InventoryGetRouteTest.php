<?php

namespace Tests\Feature;

use Tests\Feature\AuthorizedUserTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryGetRouteTest extends TestCase
{
    use AuthorizedUserTestCase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_inventory_route()
    {
        $response = $this->get('/api/inventory');

        $response->assertStatus(200);
    }
}
