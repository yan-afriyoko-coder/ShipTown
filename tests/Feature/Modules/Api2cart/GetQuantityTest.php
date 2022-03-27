<?php

namespace Tests\Feature\Modules\Api2cart;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Api2cart\src\Transformers\ProductTransformer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetQuantityTest extends TestCase
{
    use RefreshDatabase;

    private Api2cartProductLink $productLink;

    protected function setUp(): void
    {
        parent::setUp();

        factory(Warehouse::class, 10)->create();

        $product1 = factory(Product::class)->create();
        $product2 = factory(Product::class)->create();

        Inventory::query()->each(function (Inventory $inventory) {
            $inventory->update(['quantity' => 1]);
        });

        $api2cartConnection = factory(Api2cartConnection::class)->create();

        $this->productLink = new Api2cartProductLink();
        $this->productLink->api2cart_connection_id = $api2cartConnection->getKey();
        $this->productLink->product_id = $product1->getKey();
        $this->productLink->save();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testEmptyInventoryWarehouseIds()
    {
        $this->assertEquals(0, ProductTransformer::toApi2cartPayload($this->productLink)['quantity']);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSetInventoryWarehouseIds()
    {
        Warehouse::query()
            ->select('id')
            ->limit(5)
            ->inRandomOrder()
            ->get()
            ->map(function (Warehouse $warehouse) {
                $warehouse->attachTag('magento_stock');
                return $warehouse->getKey();
            })
            ->toArray();

        $this->assertEquals(5, ProductTransformer::toApi2cartPayload($this->productLink)['quantity']);
    }
}
