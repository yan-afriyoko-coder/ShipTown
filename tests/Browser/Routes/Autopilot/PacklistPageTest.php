<?php

namespace Tests\Browser\Routes\Autopilot;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class PacklistPageTest extends DuskTestCase
{
    private string $uri = '/autopilot/packlist';

    /**
     * @throws Throwable
     */
    public function testBasics()
    {
        $this->basicUserAccessTest($this->uri, true);
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicGuestAccessTest($this->uri);
    }

    /**
     * @throws Throwable
     */
    public function testPressStartAutopilot()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        /** @var OrderStatus $orderStatus */
        $orderStatus = OrderStatus::factory()->create(['name' => 'paid', 'order_active' => true]);

        /** @var Order $order */
        $order = Order::factory()->create();
        $order->update(['order_status' => $orderStatus->code]);

        /** @var OrderProduct $orderProduct */
        $orderProduct = OrderProduct::factory()->create(['order_id' => $order->getKey()]);

        $orderProduct->product
            ->inventory($user->warehouse->code)
            ->update([
                'quantity' => $orderProduct->quantity_ordered,
                'quantity_reserved' => 0,
            ]);

        $order->refresh();
        $order->update(['total_paid' => $order->total_order]);


        $this->browse(function (Browser $browser) use ($user, $order) {
            $browser->disableFitOnFailure();

            $browser->loginAs($user);
            $browser->visit($this->uri);
            $browser->waitFor('@startAutopilotButton', 1);
            $browser->click('@startAutopilotButton');
            $browser->waitForText($order->order_number, 1);
            $browser->assertSourceMissing('snotify-error');
        });
    }
}
