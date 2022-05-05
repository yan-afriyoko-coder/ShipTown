<?php

namespace Tests\Feature\ModulesNew\AutoClose;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_module_basic_functionality()
    {
        $this->markAsRisky();
    }
}
