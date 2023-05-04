<?php

namespace Tests\Browser\Routes;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class RegisterPageTest extends DuskTestCase
{
    private string $uri = '/products';

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
