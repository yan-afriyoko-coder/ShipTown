<?php

namespace Tests\Feature\Jobs\Modules\Api2cart;

use App\Jobs\Modules\Api2cart\SyncProductJob;
use App\Models\Product;
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
        factory(Api2cartConnection::class)->create();
        $product = factory(Product::class)->create();

        SyncProductJob::dispatchNow($product);

        // no errors, so we happy :)
        $this->assertTrue(true);
    }
}
