<?php

namespace Tests\Feature\Modules\Automations\Conditions;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductTotal;
use App\Modules\Automations\src\Conditions\IsFullyPickedCondition;
use Tests\TestCase;

class IsFullyPickedConditionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $order1 = factory(Order::class)->create();
        $order2 = factory(Order::class)->create();
        $order3 = factory(Order::class)->create();

        /** @var OrderProduct $orderProduct1 */
        $orderProduct1 = factory(OrderProduct::class)->create(['order_id' => $order1->getKey()]);

        /** @var OrderProduct $orderProduct2 */
        $orderProduct2 = factory(OrderProduct::class)->create(['order_id' => $order2->getKey()]);

        /** @var OrderProduct $orderProduct3 */
        $orderProduct3 = factory(OrderProduct::class)->create(['order_id' => $order3->getKey()]);

        $orderProduct1->update(['quantity_picked' => $orderProduct1->quantity_ordered]);
    }

    public function test_condition_query_scope()
    {
        $query = Order::query();

        IsFullyPickedCondition::addQueryScope($query, 'true');

        $this->assertEquals(1, $query->count());
    }

    public function test_condition_false_query_scope()
    {
        $query = Order::query();

        IsFullyPickedCondition::addQueryScope($query, 'false');

        ray(OrderProduct::all()->toArray());
        ray(OrderProductTotal::all()->toArray());

        ray($query->toSql());

        $this->assertEquals(2, $query->count());
    }
}
