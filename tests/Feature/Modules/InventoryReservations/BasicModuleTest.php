<?php

namespace Tests\Feature\Modules\InventoryReservations;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Modules\InventoryReservations\src\EventServiceProviderBase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        EventServiceProviderBase::enableModule();
    }

    public function test_if_reserves_correctly()
    {
        /** @var Order $product */
        $product = factory(Product::class)->create();

        /** @var OrderStatus $orderStatus */
        $orderStatus = factory(OrderStatus::class)->create(['order_active' => true]);

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => $orderStatus->code]);

        /** @var OrderProduct $orderProduct */
        $orderProduct = factory(OrderProduct::class)->create([
            'order_id' => $order->getKey(),
            'product_id' => $product->getKey()
        ]);

        $this->assertEquals(
            $orderProduct->quantity_to_ship,
            $orderProduct->product->inventory()->where(['warehouse_code' => 999])->first()->quantity_reserved
        );

        $orderProduct->quantity_ordered = 0;
        $orderProduct->save();

        $this->assertEquals(
            $orderProduct->quantity_to_ship,
            $orderProduct->product->inventory()->where(['warehouse_code' => 999])->first()->quantity_reserved
        );
    }
}
