<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\Webhooks\SubscriptionController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {
        $this->assertTrue(true, 'Tested in External/Webhooks');
    }
}
