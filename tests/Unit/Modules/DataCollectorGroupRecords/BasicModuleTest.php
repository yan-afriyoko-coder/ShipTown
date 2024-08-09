<?php

namespace Tests\Unit\Modules\DataCollectorGroupRecords;

use App\Modules\DataCollectorGroupRecords\src\DataCollectorGroupRecordsServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DataCollectorGroupRecordsServiceProvider::enableModule();
    }

    /** @test */
    public function testBasicFunctionality()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
