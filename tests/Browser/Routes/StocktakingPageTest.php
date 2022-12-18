<?php

namespace Tests\Browser\Routes;

use Tests\DuskTestCase;
use Throwable;

class StocktakingPageTest extends DuskTestCase
{
    private string $uri = '/stocktaking';

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

