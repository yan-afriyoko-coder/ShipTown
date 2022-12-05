<?php

namespace Tests\Browser;

use App\Models\Order;
use App\Models\OrderProduct;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class OrdersPageTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function testBasicExample()
    {
        Order::query()->forceDelete();

        $this->browse(function (Browser $browser) {
            $orderProduct = OrderProduct::factory()->count(15)->create();

            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/orders')
                ->disableFitOnFailure()
                ->pause(2000)
                ->screenshot('OrdersPage')
                ->waitForText($orderProduct->first()->order->order_number, 5)
                ->assertSee($orderProduct->first()->order->order_number);
        });
    }

    /**
     * A basic browser test example.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function test_orders_empty()
    {
        Order::query()->forceDelete();

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/orders')
                ->waitForText('No orders found.')
                ->assertSee('No orders found.');
        });
    }
}
