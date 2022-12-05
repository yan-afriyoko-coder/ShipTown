<?php

namespace Tests\External\Api2cart\Jobs;

use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Jobs\SyncProductsJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $warehouse = Warehouse::factory()->create(['code' => '99', 'name' => '99']);
        $warehouse->attachTag('magento_stock');

        $product = Product::factory()
            ->with('inventory')
            ->create();

        Api2cartConnection::factory()->create([
            'bridge_api_key'   => config('api2cart.api2cart_test_store_key'),
            'magento_store_id' => null,
            'inventory_source_warehouse_tag' => 'magento_stock',
            'pricing_source_warehouse_id' => $warehouse->getKey()
        ]);

        $product->attachTags(['Available Online', 'Not Synced']);

        $this->assertTrue(Product::withAllTags(['Not Synced'])->exists());

        SyncProductsJob::dispatchNow();
    }

    public function test_basic()
    {
//        /** @var Warehouse $warehouse */
//        $warehouse = Warehouse::factory()->create();
//        $warehouse->attachTag('api2cart');
//
//        $api2cartConnection = Api2cartConnection::factory()->create([
//            'inventory_source_warehouse_tag' => 'api2cart',
//            'pricing_source_warehouse_id' => $warehouse->getKey()
//        ]);
//
//        /** @var Api2cartProductLink $productLink1 */
//        $productLink1 = Api2cartProductLink::factory()->create([
//            'api2cart_connection_id' => $api2cartConnection->getKey(),
//            'product_id' => Product::factory()->create()->getKey(),
//            'is_in_sync' => true,
//        ]);
//
//        CheckForOutOfSyncProductsJob::dispatch();
//        $this->assertDatabaseMissing('modules_api2cart_product_links', ['is_in_sync' => true]);
//
//        UpdateMissingTypeAndIdJob::dispatch();
//
//        SyncProductsJob::dispatch();
//        FetchSimpleProductsInfoJob::dispatch();
//
//        ray()->showQueries();
//
//        CheckForOutOfSyncProductsJob::dispatch();
//        $this->assertDatabaseHas('modules_api2cart_product_links', ['is_in_sync' => true]);
//
//        Api2cartProductLink::query()->update([
//            'last_fetched_data' => null,
//            'is_in_sync' => null,
//            'api2cart_quantity' => 2
//        ]);
//        CheckForOutOfSyncProductsJob::dispatch();
//        $this->assertDatabaseMissing('modules_api2cart_product_links', ['is_in_sync' => false]);
//
//        Api2cartProductLink::query()->update(['last_fetched_data' => null]);
//        Api2cartProductLink::query()->update(['api2cart_quantity' => null]);
//        CheckForOutOfSyncProductsJob::dispatch();
//        $this->assertDatabaseMissing('modules_api2cart_product_links', ['is_in_sync' => false]);
//
//        FetchSimpleProductsInfoJob::dispatch();
//        $this->assertDatabaseMissing('modules_api2cart_product_links', ['last_fetched_data' => null]);
    }
}
