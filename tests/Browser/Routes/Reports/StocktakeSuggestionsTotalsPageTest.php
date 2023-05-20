<?php

namespace Tests\Browser\Routes\Reports;

use Exception;
use Tests\DuskTestCase;
use Throwable;

class StocktakeSuggestionsTotalsPageTest extends DuskTestCase
{
    private string $uri = '/reports/stocktake-suggestions-totals';

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

