<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class ProductModelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_reserves_correctly()
    {
        $product = Product::firstOrCreate(
            ["sku" => "12345"]
        );

        // get current values
        $previous_quantity_reserved = $product->quantity_reserved;

        $product->reserve(5,'ProductModeTest reservation');

        $this->assertEquals($previous_quantity_reserved, $product->quantity_reserved - 5);

    }
}
