<?php

namespace Tests\Feature\Modules\InventoryReservations;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_reserves_correctly()
    {
        /** @var Order $product */
        $product = factory(Product::class)->create();

        /** @var OrderProduct $orderProduct */
        $orderProduct = factory(OrderProduct::class)->create(['product_id' => $product->getKey()]);

        $this->assertEquals(
            $orderProduct->quantity_to_ship,
            $orderProduct->product->inventory()->where(['location_id' => 999])->first()->quantity_reserved
        );

        $orderProduct->quantity_ordered = 0;
        $orderProduct->save();

        $this->assertEquals(
            $orderProduct->quantity_to_ship,
            $orderProduct->product->inventory()->where(['location_id' => 999])->first()->quantity_reserved
        );
    }
}
