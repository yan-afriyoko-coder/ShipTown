<?php

namespace Tests\Unit\Widgets;

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
        Order::factory()->create(['order_closed_at' => now()]);

        $widget = new CompletedOrdersCountByDateWidget;

        $this->assertNotNull($widget->run());
    }
}
