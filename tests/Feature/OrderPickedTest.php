<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
use App\Services\PicklistService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderPickedTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Order::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        Picklist::query()->forceDelete();

        $order = factory(Order::class)->create();

        $orderProducts = factory(OrderProduct::class, 10)->create([
            'order_id' => $order['id']
        ]);

        $order->orderProducts()->saveMany($orderProducts);

        PicklistService::addOrderProductPick($order->orderProducts()->get()->toArray());

        foreach (Picklist::all() as $picklist) {
            $picklist->quantity_picked = $picklist->quantity_requested;
            $picklist->picked_at = now();
            $picklist->save();
        }

        $this->assertFalse(
            Order::query()->where(['is_picked' => false])->exists()
        );

    }
}
