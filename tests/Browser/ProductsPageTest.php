<?php

namespace Tests\Browser;

use App\Models\Product;
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
            /** @var Product $product */
            $product = factory(Product::class)->create();

            $user = factory(User::class)->create();

            $browser->loginAs($user)
                ->visit('/products')
                ->waitForText($product->name)
                ->assertSee($product->name);
        });
    }

    /**
     * A basic browser test example.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function test_products_empy()
    {
        Product::query()->forceDelete();

        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/products')
                ->waitForText('No products found.')
                ->assertSee('No products found.');
        });
    }
}
