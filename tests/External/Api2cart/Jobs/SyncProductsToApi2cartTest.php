<?php

namespace Tests\External\Api2cart\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Jobs\SyncProductsJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Tags\Tag;
use Tests\TestCase;

class SyncProductsToApi2cartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        factory(Warehouse::class)->create(['code' => '99', 'name' => '99']);

        $product = factory(Product::class)
            ->with('inventory')
            ->create();

        factory(Api2cartConnection::class)->create([
            'bridge_api_key'   => config('api2cart.api2cart_test_store_key'),
            'magento_store_id' => null,
        ]);

        $product->attachTags(['Available Online', 'Not Synced']);

        $this->assertTrue(Product::withAllTags(['Not Synced'])->exists());

        SyncProductsJob::dispatchNow();
    }
}
