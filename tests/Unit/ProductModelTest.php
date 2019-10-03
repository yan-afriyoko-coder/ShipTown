<?php

namespace Tests\Unit;

use App\Managers\ProductManager;
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
        $product_before = Product::firstOrCreate(["sku" => '0123456']);

        ProductManager::reserve(
            "0123456",
            5,
            "ProductModeTest reservation"
        );

        $product_after = $product_before->fresh();

        $this->assertEquals($product_after->quantity_reserved, $product_before->quantity_reserved + 5);

    }
}
