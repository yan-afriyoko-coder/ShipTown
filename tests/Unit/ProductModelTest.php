<?php

namespace Tests\Unit;

use App\Managers\ProductManager;
use App\Models\Product;
use App\User;
use Illuminate\Support\Collection;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class ProductModelTest extends TestCase
{
    public function test_if_quantity_available_below_0_not_allowed()
    {
        $product_before = Product::firstOrCreate(["sku" => '0123456']);

        // reserve 1 more than actually in stock
        // so quantity_available < 0
        ProductManager::reserve(
            "0123456",
            $product_before->quantity + 1,
            "ProductModeTest reservation"
        );

        $product_after = $product_before->fresh();

        $this->assertEquals(0, $product_before->quantity_available);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(
            factory(User::class)->create()
        );
    }

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
