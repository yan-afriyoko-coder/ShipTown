<?php

namespace Tests\External\Api2cart\Jobs;

use App\Events\DailyEvent;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Api2cartServiceProvider;
use App\Modules\Api2cart\src\Jobs\PushOutOfSyncPricingJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class PushOutOfSyncPricingJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     *
     * @return void
     */
    public function test_if_attaches_tag()
    {
        /** @var Product $product */
        $product = factory(Product::class)->create();

        $warehouse = factory(Warehouse::class)->create();

        $api2cartConnection = factory(Api2cartConnection::class)->create([
            'pricing_source_warehouse_id' => $warehouse->getKey()
        ]);

        factory(Api2cartProductLink::class)->create([
            'api2cart_connection_id' => $api2cartConnection->getKey(),
            'product_id' => $product->getKey(),
            'last_fetched_at' => now(),
            'api2cart_price' => rand(1, 10),
        ]);

        PushOutOfSyncPricingJob::dispatch();

        $this->assertTrue($product->hasTags(['Not Synced']));
    }

    public function test_if_dispatches_job()
    {
        Api2cartServiceProvider::enableModule();

        Bus::fake();

        DailyEvent::dispatch();

        Bus::assertDispatched(PushOutOfSyncPricingJob::class);
    }
}
