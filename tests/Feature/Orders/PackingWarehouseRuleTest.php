<?php

namespace Tests\Feature\Orders;

use App\Listeners\Order\StatusChanged\PackingStatusesRules;
use App\Models\Order;
use App\Services\PrintService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use PrintNode\Request;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PackingWarehouseRuleTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        // We want to make sure this listener gets called when
        $this->mock(PackingStatusesRules::class, function ($mock) {
            $mock->shouldReceive('handle')->once();
        });

        $order = factory(Order::class)->create();

        $order->update(['status_code' => 'picking']);
    }
}
