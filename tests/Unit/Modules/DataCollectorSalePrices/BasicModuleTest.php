<?php

namespace Tests\Unit\Modules\DataCollectorSalePrices;

use App\Modules\DataCollectorSalePrices\src\DataCollectorSalePricesServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DataCollectorSalePricesServiceProvider::enableModule();
    }

    /** @test */
    public function testBasicFunctionality()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
