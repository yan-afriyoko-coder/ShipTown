<?php

namespace Tests\Feature\Modules\AutoRestockLevels;

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
