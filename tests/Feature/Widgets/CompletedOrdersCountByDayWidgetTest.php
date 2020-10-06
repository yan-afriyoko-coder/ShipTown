<?php

namespace Tests\Feature\Widgets;

use App\Models\Order;
use App\Widgets\CompletedOrdersCountByDateWidget;
use Tests\TestCase;

class CompletedOrdersCountByDayWidgetTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        factory(Order::class)->create(['order_closed_at' => now()]);

        $widget = new CompletedOrdersCountByDateWidget();

        $this->assertNotNull($widget->run());
    }
}
