<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductModelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_reserves_correctly()
    {
        $currently_reserved = $product->quantity_reserved;

        $product->reserve(5,'This is test reservation');

        $this->assertEquals($currently_reserved + 5, $product->quantity_reserved);
    }
}
