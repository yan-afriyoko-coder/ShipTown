<?php

namespace Tests\Browser\Routes;

use App\Models\Order;
use App\User;
use Exception;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class OrdersPageTest extends DuskTestCase
{
    private string $uri = '/orders';

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
    public function testIfOrdersDisplay()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = User::factory()->create();
            $user->assignRole('user');

            /** @var Order $order */
            $order = Order::factory()->create(['status_code' => 'paid']);

            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit($this->uri)
                ->waitForText($order->order_number);
        });
    }
}

