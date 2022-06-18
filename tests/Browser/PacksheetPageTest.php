<?php

namespace Tests\Browser;

use App\Models\Order;
use App\Models\OrderProduct;
use App\User;
use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PacksheetPageTest extends DuskTestCase
{
    public function test_succesful_stocktake_action()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first() ?? factory(User::class)->create();

            /** @var Order $order */
            $order = factory(Order::class)->create();

            /** @var OrderProduct $orderProduct */
            $orderProduct = factory(OrderProduct::class)->create(['order_id' => $order->getKey()]);

            $browser->loginAs($user)
                ->visit('/order/packsheet/'. $order->getKey().'?hide_nav_bar=true')
                ->pause(500)
                ->assertFocused('@barcode-input-field')
                ->assertSee($order->order_number)
                ->waitUntilMissingText('No products found', 1)
                ->screenshot('PacksheetPage')
                ->type('@barcode-input-field', $orderProduct->sku_ordered);

                $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

                $browser->waitForText('1 x shipped');
        });
    }
}
