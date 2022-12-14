<?php

namespace Tests\Browser\Routes;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Exception;
use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class ProductsPageTest extends DuskTestCase
{
    private string $uri = 'products';

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        if (empty($this->uri)) {
            throw new Exception('Please set the $uri property in your test class.');
        }

        parent::setUp();
    }

    /**
     * A basic browser test example.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function testNoProducts()
    {
        Product::query()->forceDelete();

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit($this->uri)
                ->pause(300)
                ->waitForText('No products found.')
                ->assertSee('No products found.')
                ->assertFocused('@barcode-input-field')
                ->type('@barcode-input-field', '')
                ->keys('@barcode-input-field', [WebDriverKeys::ENTER])
                ->pause(300)
                ->assertSourceMissing('snotify-error');
        });
    }

    /**
     * @throws Throwable
     */
    public function testUserAccess()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = User::factory()->create();
            $user->assignRole('user');

            /** @var Product $product */
            $product = Product::factory()->create();

            Warehouse::factory()->create();

            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit($this->uri)
                ->pause(300)
                ->assertRouteIs($this->uri)
                ->assertSourceMissing('snotify-error')
                ->assertSee($product->name)
                ->type('@barcode-input-field', $product->sku)
                ->keys('@barcode-input-field', [WebDriverKeys::ENTER])
                ->pause(300)
                ->assertSourceMissing('snotify-error')
                ->assertFocused('@barcode-input-field');
        });
    }

    /**
     * @throws Throwable
     */
    public function testAdminAccess()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $admin */
            $admin = User::factory()->create();
            $admin->assignRole('admin');

            $browser->disableFitOnFailure()
                ->loginAs($admin)
                ->visit($this->uri)
                ->pause(300)
                ->assertRouteIs($this->uri)
                ->assertSourceMissing('snotify-error');
        });
    }

    /**
     * @throws Throwable
     */
    public function testGuestAccess()
    {
        $this->browse(function (Browser $browser) {
            $browser->disableFitOnFailure()
                ->logout()
                ->visit($this->uri)
                ->assertRouteIs('login')
                ->assertSee('Login');
        });
    }
}
