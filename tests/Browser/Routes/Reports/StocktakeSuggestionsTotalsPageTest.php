<?php

namespace Tests\Browser\Routes\Reports;

use App\Models\Warehouse;
use App\User;
use Exception;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class StocktakeSuggestionsTotalsPageTest extends DuskTestCase
{
    private string $uri = '/reports/stocktake-suggestions-totals';

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
     * @throws Throwable
     */
    public function testUserAccess()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = User::factory()->create();
            $user->assignRole('user');

            $user->warehouse()->associate(Warehouse::factory()->create());

            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit($this->uri)
                ->pause(300)
                ->assertPathIs($this->uri)
                ->assertSourceMissing('snotify-error');
        });
    }

    /**
     * @throws Throwable
     */
    public function testAdminAccess()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = User::factory()->create();
            $user->assignRole('admin');

            $user->warehouse()->associate(Warehouse::factory()->create());

            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit($this->uri)
                ->pause(300)
                ->assertPathIs($this->uri)
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

