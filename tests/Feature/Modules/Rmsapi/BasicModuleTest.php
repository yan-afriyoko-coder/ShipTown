<?php

namespace Tests\Feature\Modules\Rmsapi;

use App\Modules\Rmsapi\src\RmsapiModuleServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_module_basic_functionality()
    {
        RmsapiModuleServiceProvider::enableModule();

        $this->assertTrue(true, 'Most basic test... to be continued');
    }
}
