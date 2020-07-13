<?php

namespace Tests\Unit\Jobs\Orders;

use App\Jobs\Orders\FixAllNullProductIdsJob;
use App\Models\OrderProduct;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FixAllNullProductIdsJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_runs_without_exceptions()
    {
        // prepare
        OrderProduct::query()->delete();

        factory(Product::class, 10)->create();

        factory(OrderProduct::class, 10)->create();

        OrderProduct::query()->update([
            'product_id' => null
        ]);


        // act
        FixAllNullProductIdsJob::dispatchNow();


        // assert
        $this->assertFalse(
            OrderProduct::query()->whereNull('product_id')->exists(),
            'Null product_id still exists'
        );
    }
}
