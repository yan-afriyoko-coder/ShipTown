<?php

namespace Tests\Browser\Routes\Reports;

use Tests\DuskTestCase;
use Throwable;

class RestockingPageTest extends DuskTestCase
{
    private string $uri = '/reports/restocking';

    /**
     * @throws Throwable
     */
    public function testBasics()
    {
        $this->basicUserAccessTest($this->uri, true);
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicGuestAccessTest($this->uri);
    }
}

