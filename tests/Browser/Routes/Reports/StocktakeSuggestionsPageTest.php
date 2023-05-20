<?php

namespace Tests\Browser\Routes\Reports;

use App\User;
use Exception;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class StocktakeSuggestionsPageTest extends DuskTestCase
{
    private string $uri = '/reports/stocktake-suggestions';

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        if (empty($this->uri)) {
            throw new Exception('Please set the $uri property in your test class.');
        }

        parent::setUp();
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

