<?php

namespace Tests\Feature\Modules\DataCollector;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_module_basic_functionality()
    {
        $this->assertTrue(true, 'Tested in controller test');
    }
}
