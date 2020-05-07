<?php

namespace Tests\Feature;

use App\Listeners\Inventory\UpdateProductQuantity;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Event;
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
        Event::fake();

        // assign
        $listener = new UpdateProductQuantity();

        $product = factory(Product::class)->create();

        $quantity = rand(0,1000);

        $quantity_expected = $product->quantity + $quantity;

        // act
        $inventory = factory(Inventory::class)->create([
           "product_id" => $product->id,
           "quantity" => $quantity
        ]);

        $listener->onCreated($inventory);

        // assert
        $product = $product->fresh();

        $this->assertEquals($quantity_expected, $product->quantity);
    }

    /**
     *
     */
    public function test_if_quantity_updates_on_inventory_delete()
    {
        Event::fake();

        // assign
        $listener = new UpdateProductQuantity();

        $product = factory(Product::class)->create();

        $inventory = factory(Inventory::class)->create([
            "product_id" => $product->id
        ]);

        $listener->onCreated($inventory);

        $product = $product->fresh();

        $quantity_expected = $product->quantity - $inventory->quantity;

        // act
        $inventory->delete();

        $listener->onDeleted($inventory);

        // assert
        $product = $product->fresh();

        $this->assertEquals($quantity_expected, $product->quantity);
    }

    /**
     *
     */
    public function test_if_quantity_updates_on_inventory_update()
    {
        Event::fake();

        // assign
        $listener = new UpdateProductQuantity();

        $product = factory(Product::class)->create();

        $inventory = factory(Inventory::class)->create([
            "product_id" => $product->id
        ]);


        $listener->onCreated($inventory);

        $product = $product->fresh();

        $quantity = rand(0,1000);

        $quantity_expected = $product->quantity - $inventory->quantity + $quantity;

        // act
        $inventory->quantity = $quantity;

        $listener->onUpdated($inventory);

        // assert
        $product = $product->fresh();

        $this->assertEquals($quantity_expected, $product->quantity);
    }
}
