<?php

namespace Tests\Browser\Routes\QuickConnect;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class MagentoPageTest extends DuskTestCase
{
    private string $uri = '/quick-connect/magento';

    /**
     * @throws Throwable
     */
    public function testIncomplete()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->disableFitOnFailure();
            $browser->loginAs($user);

            $this->markTestIncomplete('This test has not been implemented yet.');
        });
    }

    /**
     * @throws Throwable
     */
    public function testBasicsAccess()
    {
        $this->basicUserAccessTest($this->uri, true);
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicGuestAccessTest($this->uri);
    }
}
