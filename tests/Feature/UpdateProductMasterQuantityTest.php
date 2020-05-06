<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class UpdateProductMasterQuantityTest
 * @package Tests\Feature
 */
class UpdateProductMasterQuantityTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function test_if_quantity_updates_on_inventory_create()
    {
       $product = factory(Product::class)->create();

       $inventory = factory(Inventory::class)->create([
           "product_id" => $product->id
       ]);

       $quantity_expected = $product->quantity + $inventory->quantity;

       $product = $product->fresh();

       $this->assertEquals($quantity_expected, $product->quantity);
    }

    /**
     *
     */
    public function test_if_quantity_updates_on_inventory_delete()
    {
        $product = factory(Product::class)->create();

        $inventory = factory(Inventory::class)->create([
            "product_id" => $product->id
        ]);

        $product = $product->fresh();

        $quantity_expected = $product->quantity - $inventory->quantity;

        $inventory->delete();

        $product = $product->fresh();

        $this->assertEquals($quantity_expected, $product->quantity);
    }

    /**
     *
     */
    public function test_if_quantity_updates_on_inventory_update()
    {
        $product = factory(Product::class)->create();

        $inventory = factory(Inventory::class)->create([
            "product_id" => $product->id
        ]);

        $product = $product->fresh();

        $quantity = rand(0,1000);

        $quantity_expected = $product->quantity - $inventory->quantity + $quantity;

        $inventory->update(['quantity' => $quantity]);

        $product = $product->fresh();

        $this->assertEquals($quantity_expected, $product->quantity);
    }
}
