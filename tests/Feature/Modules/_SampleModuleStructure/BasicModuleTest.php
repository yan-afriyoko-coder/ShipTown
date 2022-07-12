<?php

namespace Tests\Feature\Modules\_SampleModuleStructure;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_module_basic_functionality()
    {
        $this->assertTrue(true, 'This is only samples to copy from');
    }
}
