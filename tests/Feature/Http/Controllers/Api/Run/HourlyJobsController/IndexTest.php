<?php

namespace Tests\Feature\Http\Controllers\Api\Run\HourlyJobsController;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('run.hourly.jobs.index'));

        $response->assertOk();
    }
}
