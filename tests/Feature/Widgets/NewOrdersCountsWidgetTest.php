<?php

namespace Tests\Feature\Widgets;

use App\Models\Order;
use App\Widgets\NewOrdersCounts;
use Tests\TestCase;

class NewOrdersCountsWidgetTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        factory(Order::class)->create(['order_placed_at' => now()]);

        $widget = new NewOrdersCounts();

        $this->assertNotNull($widget->run());
    }
}
