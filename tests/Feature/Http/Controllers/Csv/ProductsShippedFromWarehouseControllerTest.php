<?php

namespace Tests\Feature\Http\Controllers\Csv;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Csv\ProductsShippedFromWarehouseController
 */
class ProductsShippedFromWarehouseControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get(route('warehouse_shipped.csv'));

        $response->assertOk();
    }

    // test cases...
}
