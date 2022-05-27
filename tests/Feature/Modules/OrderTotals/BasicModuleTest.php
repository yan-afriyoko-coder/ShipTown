<?php

namespace Tests\Feature\Modules\OrderTotals;

use App\Modules\OrderTotals\src\OrderTotalsServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        OrderTotalsServiceProvider::enableModule();

        $this->assertTrue(true, 'Most basic test... to be continued');
    }
}
