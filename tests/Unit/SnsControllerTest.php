<?php

namespace Tests\Unit;

use App\Http\Controllers\SnsController;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnsControllerTest extends TestCase
{
    public function test_if_topic_name_is_generated_correctly() {
        $controller = new SnsController('order_events');

        $tenantName = ENV('TENANT_NAME');

        $expectedName = $tenantName.'_'.'order_events';

        $actualName = $controller->getFullTopicName();

        $this->assertEquals($expectedName, $actualName);

    }
}
