<?php

namespace Tests\Feature\Modules\OrderStatus;

use App\Modules\OrderStatus\src\OrderStatusServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        OrderStatusServiceProvider::enableModule();

        $this->assertTrue(true, 'Most basic test... to be continued');
    }
}
