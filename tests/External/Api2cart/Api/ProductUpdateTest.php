<?php

namespace Tests\External\Api2cart\Api;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
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
    private $api2cart_product_link;

    /**
     * @var Api2cartConnection
     */
    private Api2cartConnection $api2cart_connection;

    /**
     * @var Product
     */
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $product = factory(Product::class)->create();

        $api2cart_connection = factory(Api2cartConnection::class)->create([
            'location_id' => '99',
            'pricing_location_id' => '99',
            'inventory_location_id' => '99',
            'bridge_api_key' => config('api2cart.api2cart_test_store_key'),
        ]);

        factory(Inventory::class)->create(['location_id' => '99']);

        factory(ProductPrice::class)->create(['location_id' => '99']);

        $this->api2cart_product_link = factory(Api2cartProductLink::class)->create([]);
        $this->api2cart_product_link->product()->associate($product);
        $this->api2cart_product_link->api2cartConnection()->associate($api2cart_connection);
    }

    /**
     */
    public function test_if_updates_stock_if_no_location_source_specified()
    {
        $this->api2cart_product_link->postProductUpdate();

        $this->assertTrue($this->api2cart_product_link->isInSync());
    }
}
