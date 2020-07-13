<?php

namespace Tests\Unit\Jobs\Orders;

use App\Jobs\Orders\CheckOrderProductsAndFillProductIdsJob;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckOrderProductsAndFillProductIdsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_runs_without_exceptions()
    {
        $this->expectNotToPerformAssertions();

        CheckOrderProductsAndFillProductIdsJob::dispatchNow();
    }
}
