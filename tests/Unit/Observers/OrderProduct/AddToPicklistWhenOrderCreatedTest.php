<?php

namespace Tests\Unit\Observers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
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
    public function test_if_all_products_are_added_to_picklist()
    {
        // clean up database
        Order::query()->delete();
        OrderProduct::query()->delete();
        Picklist::query()->delete();

        // make sure everything is empty
        $this->assertEquals(0, Order::query()->count());
        $this->assertEquals(0, OrderProduct::query()->count());
        $this->assertEquals(0, Picklist::query()->count());

        // create new order
        factory(Order::class,1)
            ->create()
            ->each(function ($order) {
                $orderProducts = factory(OrderProduct::class, rand(1,20))->make();

                $order->orderProducts()->saveMany($orderProducts);
            });

        // check if all quantities are added to picklist
        $this->assertEquals(
            Picklist::query()->sum('quantity_to_pick'),
            Order::query()->first()->orderProducts()->sum('quantity'),
        );

    }
}
