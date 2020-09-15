<?php

namespace Tests\Feature;

use App\Events\Inventory\CreatedEvent;
use App\Events\Inventory\DeletedEvent;
use App\Events\Inventory\UpdatedEvent;
use App\Listeners\Inventory\Created\AddToProductTotalQuantityListener;
use App\Listeners\Inventory\Deleted\DeductFromProductTotalQuantityListener;
use App\Listeners\Inventory\Updated\UpdateProductTotalQuantityListener;
use App\Listeners\Inventory\UpdateProductQuantity;
use App\Listeners\Order\StatusChanged\RemoveFromOldPicklistListener;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

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
    public function testIfQuantityUpdatesOnInventoryCreate()
    {
        Event::fake();

        // assign

        $product = factory(Product::class)->create();

        $quantity = rand(0, 1000);

        $quantity_expected = $product->quantity + $quantity;

        // act
        $inventory = factory(Inventory::class)->create([
           "product_id" => $product->id,
           "quantity" => $quantity
        ]);

        $listener = new AddToProductTotalQuantityListener();
        $listener->handle(new CreatedEvent($inventory));

        // assert
        $product = $product->fresh();

        $this->assertEquals($quantity_expected, $product->quantity);
    }

    /**
     *
     */
    public function testIfQuantityUpdatesOnInventoryDelete()
    {
        Event::fake();

        // assign

        $product = factory(Product::class)->create();

        $inventory = factory(Inventory::class)->create([
            "product_id" => $product->id
        ]);

        $listener = new AddToProductTotalQuantityListener();
        $listener->handle(new CreatedEvent($inventory));

        $product = $product->fresh();

        $quantity_expected = $product->quantity - $inventory->quantity;

        // act
        $inventory->delete();

        $listener = new DeductFromProductTotalQuantityListener();
        $listener->handle(new DeletedEvent($inventory));

        // assert
        $product = $product->fresh();

        $this->assertEquals($quantity_expected, $product->quantity);
    }

    /**
     *
     */
    public function testIfQuantityUpdatesOnInventoryUpdate()
    {
        Event::fake();

        // assign

        $product = factory(Product::class)->create();

        $inventory = factory(Inventory::class)->create([
            "product_id" => $product->id
        ]);

        $listener = new AddToProductTotalQuantityListener();
        $listener->handle(new CreatedEvent($inventory));

        $product = $product->fresh();

        $quantity = rand(0, 1000);

        $quantity_expected = $product->quantity - $inventory->quantity + $quantity;

        // act
        $inventory->quantity = $quantity;

        $listener = new UpdateProductTotalQuantityListener();
        $listener->handle(new UpdatedEvent($inventory));

        // assert
        $product = $product->fresh();

        $this->assertEquals($quantity_expected, $product->quantity);
    }
}
