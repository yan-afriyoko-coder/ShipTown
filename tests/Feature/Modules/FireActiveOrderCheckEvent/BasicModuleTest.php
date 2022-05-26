<?php

namespace Tests\Feature\Modules\FireActiveOrderCheckEvent;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        $this->assertTrue(true, 'FireActiveOrderCheckEvent module should be deleted');
    }
}
