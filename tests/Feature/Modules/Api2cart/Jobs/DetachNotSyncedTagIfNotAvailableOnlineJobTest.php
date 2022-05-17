<?php

namespace Tests\Feature\Modules\Api2cart\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\DetachNotSyncedTagIfNotAvailableOnlineJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetachNotSyncedTagIfNotAvailableOnlineJobTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $product1 = factory(Product::class)->create();
        $product2 = factory(Product::class)->create();

        $product1->attachTag('Not Synced');
        $product2->attachTag('Not Synced');

        $product1->attachTag('Available Online');

        DetachNotSyncedTagIfNotAvailableOnlineJob::dispatch();

        $this->assertEquals(1, Product::withAllTags('Not Synced')->count());
    }
}
