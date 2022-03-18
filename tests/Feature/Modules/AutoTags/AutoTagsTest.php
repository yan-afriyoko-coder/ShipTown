<?php

namespace Tests\Feature\Modules\AutoTags;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function PHPUnit\Framework\assertTrue;

class AutoTagsTest extends TestCase
{
    use RefreshDatabase;

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
