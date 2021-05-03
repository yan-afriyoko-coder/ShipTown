<?php

namespace Tests\External\Api2cart\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\SyncProductsToApi2Cart;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Tags\Tag;
use Tests\TestCase;

class SyncProductsToApi2cartTest extends TestCase
{
    use RefreshDatabase;

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
        $product = factory(Product::class)
            ->with('inventory')
            ->create();

        factory(Api2cartConnection::class)->create([
            'bridge_api_key' => config('api2cart.api2cart_test_store_key'),
            'magento_store_id' => null,
        ]);

        $product->attachTags(['Available Online', 'Not Synced']);

        $this->assertTrue(Product::withAllTags(['Not Synced'])->exists());

        SyncProductsToApi2Cart::dispatchNow();

        $this->assertFalse(Product::withAllTags(['Not Synced'])->exists(), 'Sync tag still attached');
    }
}
