<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SyncProductsToApi2Cart;
use App\Models\Inventory;
use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Spatie\Tags\Tag;
use Tests\TestCase;

class SyncProductsToApi2cartTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Api2cartConnection::query()->forceDelete();
        Inventory::query()->forceDelete();
        Product::query()->forceDelete();
        Tag::query()->forceDelete();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
//        $this->doesNotPerformAssertions();

        $product = factory(Product::class)
            ->with('inventory')
            ->create();

        factory(Api2cartConnection::class)
            ->create();

        $product->attachTags(['Available Online', 'Not Synced']);

        $this->assertTrue(Product::withAllTags(['Not Synced'])->exists());

        SyncProductsToApi2Cart::dispatchNow();

        $this->assertFalse(Product::withAllTags(['Not Synced'])->exists());
    }
}
