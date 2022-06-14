<?php

namespace Tests\Browser;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class StocktakingPageTest extends DuskTestCase
{
    public function test_succesfull_stocktake_action()
    {
        $this->fail();
    }

    public function test_if_negative_quantity_not_allowed()
    {
        $this->fail();
    }

    public function test_if_alias_scanns()
    {
        $this->fail();
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
