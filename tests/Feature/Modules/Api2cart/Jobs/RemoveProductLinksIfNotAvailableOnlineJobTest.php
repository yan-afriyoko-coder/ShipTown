<?php

namespace Tests\Feature\Modules\Api2cart\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\RemoveProductLinksIfNotAvailableOnlineJob;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Tests\TestCase;

class RemoveProductLinksIfNotAvailableOnlineJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $product1 = factory(Product::class)->create();
        $product2 = factory(Product::class)->create();

        factory(Api2cartProductLink::class)->create(['product_id' => $product1->getKey()]);
        factory(Api2cartProductLink::class)->create(['product_id' => $product2->getKey()]);

        $product2->attachTag('Available Online');

        RemoveProductLinksIfNotAvailableOnlineJob::dispatch();

        $this->assertEquals(1, Api2cartProductLink::query()->count('id'));
    }
}
