<?php

namespace Tests\Unit\Observers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
use App\Observers\AddToPicklistWhenOrderCreated;
use Sentry\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddToPicklistWhenOrderCreatedTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Order::query()->delete();
        OrderProduct::query()->delete();
        Picklist::query()->delete();

        $this->assertEquals(0, Order::query()->count());
        $this->assertEquals(0, OrderProduct::query()->count());
        $this->assertEquals(0, Picklist::query()->count());

        $order = factory(Order::class,1)
            ->create()
            ->each(function ($order) {
                $orderProducts = factory(OrderProduct::class, rand(1,20))->make();

                $order->orderProducts()->saveMany($orderProducts);
            });

        $this->assertEquals(
            Picklist::query()->sum('quantity_to_pick'),
            Order::query()->first()->orderProducts()->sum('quantity'),
        );

    }
}
