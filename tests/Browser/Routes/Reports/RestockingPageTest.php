<?php

namespace Tests\Browser\Routes\Reports;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class RestockingPageTest extends DuskTestCase
{
    private string $uri = '/reports/restocking';

    /**
     * @throws Throwable
     */
    public function testUserAccess()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = User::factory()->create();
            $user->assignRole('user');

            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit($this->uri)
                ->assertDontSee('SERVER ERROR')
                ->pause(1000)
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
            /** @var User $admin */
            $admin = User::factory()->create();
            $admin->assignRole('admin');

            $browser->disableFitOnFailure()
                ->loginAs($admin)
                ->visit($this->uri)
                ->assertDontSee('SERVER ERROR')
                ->pause(1000)
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
                ->assertPathIs('/login')
                ->assertSee('Login');
        });
    }
}

