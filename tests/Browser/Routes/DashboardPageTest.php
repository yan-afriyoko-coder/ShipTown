<?php

namespace Tests\Browser\Routes;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class DashboardPageTest extends DuskTestCase
{
    private string $uri = 'dashboard';

    /**
     * @throws Throwable
     */
    public function testUserAccess()
    {
        $this->browse(function (Browser $b) {
            /** @var User $user */
            $user = User::factory()->create();
            $user->assignRole('user');

            $b->disableFitOnFailure();
            $b->loginAs($user);
            $b->visit($this->uri);
            $b->pause(300);
            $b->assertRouteIs($this->uri);
            $b->assertSourceMissing('snotify-error');
            $b->assertSee('Orders - Packed');
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

            $this->browse(function (Browser $b) {
                /** @var User $user */
                $user = User::factory()->create();
                $user->assignRole('user');

                $b->disableFitOnFailure();
                $b->loginAs($user);
                $b->visit($this->uri);
                $b->pause(300);
                $b->assertRouteIs($this->uri);
                $b->assertSourceMissing('snotify-error');
                $b->assertSee('Orders - Packed');
            });
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

