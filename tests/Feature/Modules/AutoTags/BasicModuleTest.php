<?php

namespace Tests\Feature\Modules\AutoTags;

use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\AutoTags\src\EventServiceProviderBase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        \App\Modules\InventoryReservations\src\EventServiceProviderBase::enableModule();
        EventServiceProviderBase::enableModule();
    }

    public function test_if_attaches_tag()
    {
        $warehouse = factory(Warehouse::class)->create();

        /** @var Product $product */
        $product = factory(Product::class)->create();

        $product->inventory->first()->update([
            'quantity' => 0,
            'quantity_reserved' => 1
        ]);

        $product = $product->refresh();

        $this->assertTrue($product->hasTags(['oversold']));
    }

    public function test_if_detaches_tag()
    {
        $warehouse = factory(Warehouse::class)->create();

        /** @var Product $product */
        $product = factory(Product::class)->create();

        $product->inventory->first()->update([
            'quantity' => 0,
            'quantity_reserved' => 0
        ]);

        $this->assertFalse($product->hasTags(['oversold']));
    }
}
