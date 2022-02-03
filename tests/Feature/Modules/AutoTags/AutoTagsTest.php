<?php

namespace Tests\Feature\Modules\AutoTags;

use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function PHPUnit\Framework\assertTrue;

class AutoTagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_attaches_tag()
    {
        /** @var Inventory $inventory */
        $inventory = factory(Inventory::class)->create([]);

        $inventory->update([
            'quantity' => 0,
            'quantity_reserved' => 1
        ]);

        $product = $inventory->product->refresh();

        $this->assertTrue($product->hasTags(['oversold']));
    }
    public function test_if_detaches_tag()
    {
        /** @var Inventory $inventory */
        $inventory = factory(Inventory::class)->create([]);

        $inventory->update([
            'quantity' => 0,
            'quantity_reserved' => 0
        ]);

        $this->assertFalse($inventory->product->hasTags(['oversold']));
    }
}
