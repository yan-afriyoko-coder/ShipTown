<?php

namespace Tests\Browser\Routes\Admin\Settings\Modules\Webhooks;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class SubscriptionsPageTest extends DuskTestCase
{
    private string $uri = '';

    /**
     * @throws Throwable
     */
    public function testUserAccess()
    {
        if (empty($this->uri)) {
            $this->markTestIncomplete('URI is not set');
        }

        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = User::factory()->create();
            $user->assignRole('user');

            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit($this->uri)
                ->pause(300)
                ->assertSourceMissing('snotify-error')
                ->assertSourceMissing('snotify-error')
                ->pause(1);
        });
    }

    /**
     * @throws Throwable
     */
    public function testAdminAccess()
    {
        if (empty($this->uri)) {
            $this->markTestIncomplete('URI is not set');
        }

        $this->browse(function (Browser $browser) {
            /** @var User $admin */
            $admin = User::factory()->create();
            $admin->assignRole('admin');

            $browser->disableFitOnFailure()
                ->loginAs($admin)
                ->visit($this->uri)
                ->pause(300)
                ->assertSourceMissing('snotify-error')
                ->pause(1);
        });
    }

    /**
     * @throws Throwable
     */
    public function testGuestAccess()
    {
        if (empty($this->uri)) {
            $this->markTestIncomplete('URI is not set');
        }

        $this->browse(function (Browser $browser) {
            $browser->disableFitOnFailure()
                ->logout()
                ->visit($this->uri)
                ->assertRouteIs('login')
                ->assertSee('Login');
        });
    }
}
