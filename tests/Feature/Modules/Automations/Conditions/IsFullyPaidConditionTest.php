<?php

namespace Tests\Feature\Modules\Automations\Conditions;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductTotal;
use App\Modules\Automations\src\Conditions\Order\IsFullyPaidCondition;
use Tests\TestCase;

class IsFullyPaidConditionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();


        // scenario 2: order2 is partially paid (shipping not paid)
        /** @var Order $order2 */
        $order2 = Order::factory()->create();
        OrderProduct::factory()->create(['order_id' => $order2->getKey()]);
        $order2->update(['total_shipping' => 10, 'total_paid' => $order2->orderProductsTotals->total_price]);

        // scenario 3: order3 is not paid at all
        /** @var Order $order3 */
        $order3 = Order::factory()->create();
        OrderProduct::factory()->create(['order_id' => $order3->getKey()]);

        // scenario 4: order4 is not paid but also has 0 total, should be considered as NOT PAID
        /** @var Order $order4 */
        $order4 = Order::factory()->create();
        OrderProduct::factory()->create(['order_id' => $order4->getKey(), 'price' => 0]);

        // scenario 5: order5 is paid but 0 total, it should be considered as PAID
        /** @var Order $order3 */
        $order5 = Order::factory()->create();
        OrderProduct::factory()->create(['order_id' => $order5->getKey(), 'price' => 0]);
        $order5->update(['total_paid' => 100]);
    }

    public function test_order_fully_paid()
    {
        Order::query()->forceDelete();

        /** @var Order $order */
        $order = Order::factory()->create();
        OrderProduct::factory()->create(['order_id' => $order->getKey()]);
        $order = $order->refresh();
        $order->update(['total_paid' => $order->total_order]);

        $query = Order::query();

        IsFullyPaidCondition::addQueryScope($query, 'true');

        ray($order->toArray());
        ray($query->get()->toArray());

        $this->assertEquals(1, $query->count(), 'Order has not been returned as paid');
    }

    public function test_order_paid_with_discounts()
    {
        Order::query()->forceDelete();

        $query = Order::query();

        IsFullyPaidCondition::addQueryScope($query, 'true');

        /** @var Order $order */
        $order = Order::factory()->create();
        OrderProduct::factory()->create(['order_id' => $order->getKey()]);

        $order->update(['total_discounts' => $order->orderProductsTotals->total_price]);

        ray($order->refresh()->toArray());
        ray($query->toSql());
        ray($query->get()->toArray());

        $this->assertEquals(1, $query->count(), 'Order has not been returned as paid');
    }

    public function test_paid_orders_query()
    {
        $query = Order::query();

        IsFullyPaidCondition::addQueryScope($query, 'true');

        ray($query->get()->toArray());
        ray($query->toSql());

        $this->assertEquals(3, $query->count(), 'Incorrect number of orders is coming up as paid');
    }

    public function test_unpaid_orders_query()
    {
        $query = Order::query();

        IsFullyPaidCondition::addQueryScope($query, 'false');

        ray(Order::all()->toArray());
        ray(OrderProduct::all()->toArray());
        ray(OrderProductTotal::all()->toArray());

        ray($query->toSql());

        $this->assertEquals(3, $query->count(), 'Incorrect number of orders is coming up as unpaid');
    }
}
