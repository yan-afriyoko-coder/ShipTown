<?php

namespace Tests\Browser\Routes\Admin\Settings;

use App\User;
use Exception;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class UsersPageTest extends DuskTestCase
{
    private string $uri = '/admin/settings/users';

    public function testIfPageLoads()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $admin */
            $admin = User::factory()->create();
            $admin->assignRole('admin');

            $browser->loginAs($admin)
                ->disableFitOnFailure()
                ->visit($this->uri)
                ->pause(300)
                ->assertSee('Users')
                ->assertSee($admin->name);
        });
    }
}

