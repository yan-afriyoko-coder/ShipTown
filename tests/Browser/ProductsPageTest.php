<?php

namespace Tests\Browser;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class ProductsPageTest extends DuskTestCase
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
        $this->browse(function (Browser $browser) {
            Warehouse::factory()->create();

            /** @var Product $product */
            $product = Product::factory()->create();

            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/products')
                ->waitForText($product->name)
                ->assertSee($product->name)
                ->screenshot('ProductsPage');
        });
    }

    /**
     * A basic browser test example.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function test_products_empty()
    {
        Product::query()->forceDelete();

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/products')
                ->waitForText('No products found.')
                ->assertSee('No products found.');
        });
    }
}
