<?php

namespace Tests\Browser\Routes;

use Tests\DuskTestCase;
use Throwable;

class HelpPageTest extends DuskTestCase
{
    private string $uri = '/help';

    /**
     * @throws Throwable
     */
    public function testBasics()
    {
        $this->basicUserAccessTest($this->uri, true);
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicGuestAccessTest($this->uri, true);
    }

    public function testIncomplete()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
