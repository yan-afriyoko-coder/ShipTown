<?php

namespace Tests\Browser\Routes;

use App\User;
use Exception;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class FulfilmentDashboardPageTest extends DuskTestCase
{
    private string $uri = '/fulfilment-dashboard';

    /**
     * @throws Throwable
     */
    public function testBasics()
    {
        $this->basicUserAccessTest($this->uri, true);
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicGuestAccessTest($this->uri);
    }

    public function testIncomplete()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}

