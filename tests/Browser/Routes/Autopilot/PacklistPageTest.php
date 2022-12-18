<?php

namespace Tests\Browser\Routes\Autopilot;

use Tests\DuskTestCase;
use Throwable;

class PacklistPageTest extends DuskTestCase
{
    private string $uri = '/autopilot/packlist';

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

