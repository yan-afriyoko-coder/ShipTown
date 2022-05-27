<?php

namespace Tests\Feature\Modules\MagentoApi;

use App\Modules\MagentoApi\src\EventServiceProviderBase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        EventServiceProviderBase::enableModule();

        $this->assertTrue(true, 'Most basic test... to be continued');
    }
}
