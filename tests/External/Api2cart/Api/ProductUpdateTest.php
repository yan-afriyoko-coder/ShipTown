<?php

namespace Tests\External\Api2cart\Api;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Jobs\SyncProduct;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Api2cart\src\Services\Api2cartService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ProductUpdateTest
 * @package Tests\External\Api2cart\Api
 */
class ProductUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Api2cartProductLink
     */
    private Api2cartProductLink $api2cart_product_link;

    /**
     * @var Api2cartConnection
     */
    private Api2cartConnection $api2cart_connection;

    /**
     * @var Product
     */
    private Product $product;

    /**
     * @var Inventory
     */
    private Inventory $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create(['code' => '99', 'name' => '99']);

        $warehouse->attachTag('magento_stock');

        $product = Product::factory()->create();

        $api2cart_connection = Api2cartConnection::factory()->create([
            'pricing_location_id' => '99',
            'bridge_api_key' => config('api2cart.api2cart_test_store_key'),
            'inventory_source_warehouse_tag' => 'magento_stock',
            'pricing_source_warehouse_id' => $warehouse->getKey()
        ]);

        $this->api2cart_product_link = Api2cartProductLink::factory()->create([]);
        $this->api2cart_product_link->product()->associate($product);
        $this->api2cart_product_link->api2cartConnection()->associate($api2cart_connection);
    }

    /**
     */
    public function test_if_updates_stock_if_no_location_source_specified()
    {
        $this->api2cart_product_link->product->attachTags(['Not Synced']);

        $job = new SyncProduct($this->api2cart_product_link);
        $job->handle();

        $this->assertFalse($this->api2cart_product_link->product->hasTags(['Not Synced']));
    }
}
