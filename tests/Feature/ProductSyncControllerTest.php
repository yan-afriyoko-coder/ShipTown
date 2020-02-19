<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductSyncControllerTest extends TestCase
{
    /**
     * @return void
     */
    public function test_route()
    {
        $response = $this->get('/products/123456/sync');

        $response->assertStatus(302);
    }
}
