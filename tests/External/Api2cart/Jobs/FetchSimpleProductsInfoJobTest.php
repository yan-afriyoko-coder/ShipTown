<?php

namespace Tests\External\Api2cart\Jobs;

use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Jobs\FetchSimpleProductsInfoJob;
use App\Modules\Api2cart\src\Jobs\SyncProductsJob;
use App\Modules\Api2cart\src\Jobs\UpdateMissingTypeAndIdJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Tests\TestCase;

class FetchSimpleProductsInfoJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @throws RequestException
     *
     * @return void
     */
    public function testExample()
    {
        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();

        $api2cartConnection = factory(Api2cartConnection::class)->create([
            'pricing_source_warehouse_id' => $warehouse->getKey()
        ]);

        $productLink1 = factory(Api2cartProductLink::class)->create([
            'api2cart_connection_id' => $api2cartConnection->getKey(),
            'product_id' => factory(Product::class)->create()->getKey(),
        ]);

        $productLink2 = factory(Api2cartProductLink::class)->create([
            'api2cart_connection_id' => $api2cartConnection->getKey(),
            'product_id' => factory(Product::class)->create()->getKey(),
        ]);

        UpdateMissingTypeAndIdJob::dispatch();

        SyncProductsJob::dispatch();

        Api2cartProductLink::query()->update(['last_fetched_data' => null]);

        FetchSimpleProductsInfoJob::dispatch();

        $this->assertDatabaseMissing('modules_api2cart_product_links', ['last_fetched_data' => null]);
    }
}
