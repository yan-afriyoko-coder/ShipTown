<?php

namespace Tests\Browser;

use App\Models\Product;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class ActivityLogPageTest extends DuskTestCase
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
            $product = Product::factory()->create();

            $user = User::factory()->create();
            $user->assignRole('admin');

            $browser->loginAs($user)
                ->visit('/admin/activity-log')
                ->assertAuthenticated()
                ->screenshot('ActivityLog');
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

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/products')
                ->waitForText('No products found.')
                ->assertSee('No products found.');
        });
    }
}
