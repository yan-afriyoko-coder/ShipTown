<?php

namespace Tests\Browser;

use App\Models\Product;
use App\Models\ProductAlias;
use App\User;
use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class StocktakingPageTest extends DuskTestCase
{
    public function test_succesful_stocktake_action()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first() ?? factory(User::class)->create();

            $browser->loginAs($user)
                ->visit('/stocktaking')
                ->assertFocused('@barcode-input-field');

            while (rand(1, 3) !== 1) {
                /** @var Product $product */
                $product = Product::query()->inRandomOrder()->first() ?? factory(Product::class)->create();

                $browser->assertFocused('@barcode-input-field')
                    ->screenshot('StocktakingPage');

                $browser->driver->getKeyboard()->sendKeys($product->sku);
                $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

                $browser->waitFor('#quantity-request-input')
                    ->pause(500)
                    ->assertFocused('#quantity-request-input')
                    ->assertSee($product->sku)
                    ->assertSee($product->name);

                $browser->driver->getKeyboard()->sendKeys(rand(0, 10000));
                $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

                $browser->waitForText('Stocktake updated')
                    ->assertMissing('#quantity-request-input')
                    ->pause(500)
                    ->assertFocused('@barcode-input-field');
            }
        });
    }

    public function test_if_negative_quantity_not_allowed()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first() ?? factory(User::class)->create();

            $browser->loginAs($user)
                ->visit('/stocktaking')
                ->assertFocused('@barcode-input-field');

            /** @var Product $product */
            $product = Product::query()->inRandomOrder()->first() ?? factory(Product::class)->create();

            $browser->assertFocused('@barcode-input-field');

            $browser->driver->getKeyboard()->sendKeys($product->sku);
            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

            $browser->waitFor('#quantity-request-input')
                ->pause(500)
                ->assertFocused('#quantity-request-input')
                ->assertSee($product->sku)
                ->assertSee($product->name);

            $browser->driver->getKeyboard()->sendKeys(-1);
            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

            $browser->waitForText('Minus quantity not allowed')
                ->assertVisible('#quantity-request-input')
                ->pause(500)
                ->assertFocused('@quantity-request-input');
        });
    }

    public function test_if_alias_scans()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first() ?? factory(User::class)->create();

            $browser->loginAs($user)
                ->visit('/stocktaking')
                ->assertFocused('@barcode-input-field');

            /** @var ProductAlias $alias */
            $alias = ProductAlias::query()->inRandomOrder()->first() ?? factory(ProductAlias::class)->create();

            $browser->assertFocused('@barcode-input-field');

            $browser->driver->getKeyboard()->sendKeys($alias->product->sku);
            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

            $browser->waitFor('#quantity-request-input')
                ->pause(500)
                ->assertFocused('#quantity-request-input')
                ->assertSee($alias->product->sku)
                ->assertSee($alias->product->name);

            $browser->driver->getKeyboard()->sendKeys(rand(0, 10000));
            $browser->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

            $browser->waitForText('Stocktake updated')
                ->assertMissing('#quantity-request-input')
                ->pause(500)
                ->assertFocused('@barcode-input-field');
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @throws Throwable
     */
    public function test_if_notifies_when_product_not_found()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();

            $browser
                ->loginAs($user)
                ->visit('/stocktaking')
                ->assertFocused('@barcode-input-field')
                ->type('@barcode-input-field', 'not-existing-sku')
                ->keys('@barcode-input-field', '{enter}')
                ->waitForText('Product not found');
        });
    }
}
