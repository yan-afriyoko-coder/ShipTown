<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SyncProductsToApi2Cart;
use App\Models\Inventory;
use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Tests\TestCase;

class SyncProductsToApi2cartTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Api2cartConnection::query()->forceDelete();
        Inventory::query()->forceDelete();
        Product::query()->forceDelete();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->doesNotPerformAssertions();

        $product = factory(Product::class)
            ->with('inventory')
            ->create();

        factory(Api2cartConnection::class)
            ->create();

        $product->attachTags(['Available Online', 'Not Synced']);

        SyncProductsToApi2Cart::dispatchNow();
    }
}
