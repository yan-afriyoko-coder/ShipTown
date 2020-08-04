<?php

namespace Tests\Unit\Jobs\Orders;

use App\Jobs\Orders\FixAllNullProductIdsJob;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Event;
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
        Event::fake();

        // prepare
        OrderProduct::query()->delete();

        factory(Product::class, 10)->create();

        $order = factory(Order::class)->create();

        $order->orderProducts()->saveMany(
            factory(OrderProduct::class, 10)->make()
        );

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
