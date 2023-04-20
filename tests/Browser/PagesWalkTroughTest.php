<?php

namespace Tests\Browser;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Facebook\WebDriver\Exception\ElementClickInterceptedException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class PagesWalkTroughTest extends DuskTestCase
{
    private Order $order;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        $this->user = User::factory()->create(['password' => bcrypt('password')]);
        $this->user->warehouse()->associate($warehouse);

        $this->order = Order::factory()->create(['status_code' => 'paid']);

        $product1 = Product::factory()->create(['sku' => '111576']);
        $product2 = Product::factory()->create(['sku' => '222957']);

        OrderProduct::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product1->getKey(),
            'quantity_ordered' => 1
        ]);

        OrderProduct::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product2->getKey(),
            'quantity_ordered' => 3
        ]);
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @throws Throwable
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $this->transferIn($browser);
            $this->stocktaking($browser);
            $this->picklist($browser);
            $this->packlist($browser);
            $this->dashboard($browser);

//                 Products
//                ->mouseover('#products_link')->pause(200)->clickLink('Products')->pause(1000)

//                // Orders
//                ->mouseover('#orders_link')->pause(200)->clickLink('Orders')->pause(1000)

//                // Restocking
//                ->mouseover('#tools_link')->pause(200)->clickLink('Tools')->pause(200)
//                ->mouseover('#restocking_link')->pause(200)->clickLink('Restocking')->pause(1000)
//
        });
    }

    /**
     * @param Browser $browser
     * @throws ElementClickInterceptedException
     * @throws NoSuchElementException
     */
    private function packlist(Browser $browser): void
    {
        $browser->mouseover('#navToggleButton')->pause(200)->click('#navToggleButton')->pause(200)
            ->mouseover('#packlists_link')->pause(200)->clickLink('Packlist')->pause(200)
            ->pause(200)->clickLink('Status: paid')->pause(1000)
            ->assertSee('Start AutoPilot Packing')
            ->click('@startAutopilotButton')
            ->pause(1000);

        while ($this->order->orderProducts()->where('quantity_to_ship', '>', 0)->exists()) {
            $orderProduct = $this->order->orderProducts()
                ->where('quantity_to_ship', '>', 0)
                ->orderBy('id')
                ->get()
                ->first();

                $browser->assertSee($orderProduct->product->sku);

                $browser->driver->getKeyboard()->sendKeys($orderProduct->product->sku);
                $browser->pause(200)
                    ->keys('#barcodeInput', '{enter}')
                    ->pause(1500);
        }

        $browser->pause(2000)
            ->keys('#shipping_number_input', 'CB100023444')->pause(500)
            ->keys('#shipping_number_input', '{enter}')
            ->pause(1000);
    }

    /**
     * @param Browser $browser
     */
    private function login(Browser $browser): void
    {
        $browser->visit('/')->pause(500)
            ->assertPathIs('/login')
            ->type('email', $this->user->email)->pause(200)
            ->type('password', 'password')->pause(200)
            ->press('Login')->pause(1000)
            ->assertPathBeginsWith('/dashboard')
            ->pause(100);
    }

    /**
     * @param Browser $browser
     * @throws ElementClickInterceptedException
     * @throws NoSuchElementException
     */
    private function transferIn(Browser $browser): void
    {
        $browser->mouseover('#tools_link')->pause(200)->clickLink('Tools')->pause(200)
            ->mouseover('#data_collector_link')->pause(200)->clickLink('Data Collector')->pause(1000)
            ->click('#new_data_collection')->pause(1000)
            ->typeSlowly('#collection_name_input', 'Stock delivery')->pause(200)
            ->press('OK')->pause(1000)
            ->mouseover('@data_collection_record')->click('@data_collection_record')
            ->pause(1000);

        $this->order->orderProducts()
            ->where('quantity_to_ship', '>', 0)
            ->first()
            ->each(function (OrderProduct $orderProduct) use ($browser) {

                $browser->driver->getKeyboard()->sendKeys($orderProduct->product->sku);

                $browser->keys('#barcodeInput', '{enter}')->pause(2000)
                    ->typeSlowly('#data-collection-record-quantity-request-input', 12)->pause(200)
                    ->keys('#data-collection-record-quantity-request-input', '{enter}')
                    ->pause(1000);
            });

        $browser->mouseover('#showConfigurationButton')->pause(200)->click('#showConfigurationButton')->pause(1000)
            ->mouseover('#transferInButton')->pause(500)->click('#transferInButton')->pause(1000)
            ->pause(1000);
    }

    /**
     * @param Browser $browser
     * @throws ElementClickInterceptedException
     * @throws NoSuchElementException
     */
    private function picklist(Browser $browser): void
    {
        $browser->mouseover('#navToggleButton')->pause(200)->click('#navToggleButton')->pause(200)
            ->mouseover('#picklists_link')->pause(200)->clickLink('Picklist')->pause(200)
            ->pause(200)->clickLink('Status: paid')->pause(1000);

        $this->order->orderProducts()
            ->where('quantity_to_pick', '>', 0)
            ->first()
            ->each(function (OrderProduct $orderProduct) use ($browser) {
                $browser->assertSee($orderProduct->product->sku);
                $browser->driver->getKeyboard()->sendKeys($orderProduct->product->sku);
                $browser->pause(500)
                    ->keys('#barcodeInput', '{enter}')
                    ->pause(1000);
            });
    }

    /**
     * @param Browser $browser
     * @throws ElementClickInterceptedException
     * @throws NoSuchElementException
     */
    private function dashboard(Browser $browser): void
    {
// Dashboard
        $browser->mouseover('#dashboard_link')->pause(200)->click('#dashboard_link')->pause(1000)
            ->pause(1000);
    }

    /**
     * @param Browser $browser
     */
    private function stocktaking(Browser $browser): void
    {
// Stocktaking
        $browser->mouseover('#tools_link')->pause(200)->clickLink('Tools')->pause(200)
            ->mouseover('#stocktaking_link')->pause(200)->clickLink('Stocktaking')->pause(200)
            ->pause(1000);
    }
}
