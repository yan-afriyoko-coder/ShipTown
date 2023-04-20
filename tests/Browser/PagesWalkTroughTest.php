<?php

namespace Tests\Browser;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Warehouse;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class PagesWalkTroughTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @throws Throwable
     */
    public function testExample()
    {
        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        /** @var User $user */
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $user->warehouse()->associate($warehouse);

        /** @var Order $order */
        $order = Order::factory()->create(['status_code' => 'paid']);

        /** @var OrderProduct $orderProduct */
        $orderProduct = OrderProduct::factory()->create(['order_id' => $order->id, 'quantity_ordered' => 2]);

        $this->browse(function (Browser $browser) use ($user, $orderProduct) {
            $browser->visit('/')->pause(500)

                // Login
                ->assertPathIs('/login')
                ->type('email', $user->email)->pause(200)
                ->type('password', 'password')->pause(200)
                ->press('Login')->pause(1000)
                ->assertPathBeginsWith('/dashboard')

//                 Products
//                ->mouseover('#products_link')->pause(200)->clickLink('Products')->pause(1000)

//                // Orders
//                ->mouseover('#orders_link')->pause(200)->clickLink('Orders')->pause(1000)
//
//                // Stocktaking
//                ->mouseover('#tools_link')->pause(200)->clickLink('Tools')
//                ->mouseover('#stocktaking_link')->pause(200)->clickLink('Stocktaking')->pause(1000)
//
//                // Restocking
//                ->mouseover('#tools_link')->pause(200)->clickLink('Tools')->pause(200)
//                ->mouseover('#restocking_link')->pause(200)->clickLink('Restocking')->pause(1000)
//
                // Data Collector
                ->mouseover('#tools_link')->pause(200)->clickLink('Tools')->pause(200)
                ->mouseover('#data_collector_link')->pause(200)->clickLink('Data Collector')->pause(1000)
                ->click('#new_data_collection')->pause(1000)
                ->typeSlowly('#collection_name_input', 'Stock delivery')->pause(200)
                ->press('OK')->pause(1000)
                ->mouseover('@data_collection_record')->click('@data_collection_record')->pause(1000)
                ->typeSlowly('#barcodeInput', $orderProduct->product->sku)->pause(200)
                ->keys('#barcodeInput', '{enter}')->pause(2000)
                ->typeSlowly('#data-collection-record-quantity-request-input', 12)->pause(200)
                ->keys('#data-collection-record-quantity-request-input', '{enter}')->pause(1000)
                ->mouseover('#showConfigurationButton')->pause(200)->click('#showConfigurationButton')->pause(1000)
                ->mouseover('#transferInButton')->pause(500)->click('#transferInButton')->pause(1000)
                ->pause(1000)


                // Picklist
                ->mouseover('#picklists_link')->pause(200)->clickLink('Picklist')->pause(200)
                ->pause(200)->clickLink('Status: paid')->pause(1000)
                ->assertSee($orderProduct->product->sku)
                ->typeSlowly('#barcodeInput', $orderProduct->product->sku)->pause(200)
                ->keys('#barcodeInput', '{enter}')->pause(1000)

                // Picklist
                ->mouseover('#packlists_link')->pause(200)->clickLink('Packlist')->pause(200)
                ->pause(200)->clickLink('Status: paid')->pause(1000)
                ->assertSee('Start AutoPilot Packing')
                ->click('@startAutopilotButton')->pause(1000)
                ->assertSee($orderProduct->product->sku)
                ->keys('#barcodeInput', $orderProduct->product->sku)->pause(200)
                ->keys('#barcodeInput', '{enter}')->pause(400)
                ->keys('#barcodeInput', $orderProduct->product->sku)->pause(200)
                ->keys('#barcodeInput', '{enter}')->pause(400)
                ->keys('#shipping_number_input', 'CB100023444')->pause(400)
                ->keys('#shipping_number_input', '{enter}')->pause(400)
                ->pause(2000)

                // Dashboard
                ->mouseover('#dashboard_link')->pause(200)->click('#dashboard_link')->pause(1000)
                ->pause(1000);
        });
    }
}
