<?php

namespace Tests\Feature\Jobs\Modules\Api2cart;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Jobs\SyncProduct;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Tests\TestCase;

class SyncProductJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Api2cartConnection::query()->forceDelete();

        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();

        $product = factory(Product::class)->create();

        $connection = factory(Api2cartConnection::class)->create(
            [
                'pricing_location_id'   => $warehouse->code,
                'inventory_location_id' => $warehouse->code,
            ]
        );

        Api2cartProductLink::where(['product_id' => $product->getKey()])
            ->each(function (Api2cartProductLink $product_link) {
                SyncProduct::dispatchNow($product_link);
            });

        // no errors, so we happy :)
        $this->assertTrue(true);
    }
}
