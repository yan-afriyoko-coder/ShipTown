<?php

namespace Tests\External\Api2cart;

use App\Jobs\Modules\Api2cart\CompareMagentoJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareMagentoJobTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        factory(Api2cartConnection::class)->create();

        CompareMagentoJob::dispatchNow();

        // we just want to make sure there was no errors
        $this->assertTrue(true);
    }
}
