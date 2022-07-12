<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class InventoryDashboardPageTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @throws Throwable
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();

            $browser->loginAs($user)
                    ->visit('/reports/inventory-dashboard')
                    ->assertSee('Inventory Dashboard');
        });
    }
}
