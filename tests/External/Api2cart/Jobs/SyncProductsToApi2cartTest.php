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
        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create(['code' => '99', 'name' => '99']);
        $warehouse->attachTag('magento_stock');

        $product = factory(Product::class)
            ->with('inventory')
            ->create();

        factory(Api2cartConnection::class)->create([
            'bridge_api_key'   => config('api2cart.api2cart_test_store_key'),
            'magento_store_id' => null,
            'inventory_source_warehouse_tag' => 'magento_stock',
            'pricing_source_warehouse_id' => $warehouse->getKey()
        ]);

        $product->attachTags(['Available Online', 'Not Synced']);

        $this->assertTrue(Product::withAllTags(['Not Synced'])->exists());

        SyncProductsJob::dispatchNow();
    }
}
