<?php

namespace Tests\Feature\Jobs\Modules\Api2cart;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Api2cart\src\Jobs\SyncProductJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
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

        $product = factory(Product::class)->create();

        $inventory = factory(Inventory::class)->create(['product_id' => $product->getKey()]);

        $pricing = factory(ProductPrice::class)->create(['product_id' => $product->getKey()]);

        $connection = factory(Api2cartConnection::class)->create(
            [
                'pricing_location_id' => $pricing->location_id,
                'inventory_location_id' => $inventory->location_id,
            ]
        );

        SyncProductJob::dispatchNow($product);

        // no errors, so we happy :)
        $this->assertTrue(true);
    }
}
