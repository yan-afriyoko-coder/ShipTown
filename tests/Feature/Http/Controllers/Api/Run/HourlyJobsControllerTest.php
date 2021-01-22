<?php

namespace Tests\Feature\Http\Controllers\Api\Run;

use App\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Run\HourlyJobsController
 */
class HourlyJobsControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('run.hourly.jobs.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            // TODO: compare expected response data
        ]);

        // TODO: perform additional assertions
    }

    // test cases...
}
