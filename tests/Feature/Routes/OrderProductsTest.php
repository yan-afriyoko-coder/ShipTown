<?php

namespace Tests\Feature\Routes;

use App\Models\Order;
use App\Models\OrderProduct;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OrderProductsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testQuantityShippedUpdate()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        Order::query()->forceDelete();
        OrderProduct::query()->forceDelete();

        factory(Order::class)
            ->with('orderProducts')
            ->create();

        $orderProduct = OrderProduct::first();

        $response = $this->put('/api/order/products/'.$orderProduct->getKey(), [
            'quantity_shipped' => $orderProduct->quantity_ordered,
        ]);

        $response->assertStatus(200);
    }
}
