<?php

namespace Tests\Feature\Modules\Webhooks;

use App\Modules\Webhooks\src\WebhooksServiceProviderBase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        WebhooksServiceProviderBase::enableModule();

        $this->assertTrue(true, 'Most basic test... to be continued');
    }
}
