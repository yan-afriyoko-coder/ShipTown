<?php

namespace Tests\Feature\Http\Controllers\Api\RunScheduledJobsController;

use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private string $uri = 'api/run-scheduled-jobs';

    /** @test */
    public function testIfCallReturnsOk()
    {
        $user = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->postJson($this->uri, []);

        ray($response->json());

        $response->assertSuccessful();
    }

    /** @test */
    public function testUserAccess()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->postJson($this->uri, []);

        $response->assertForbidden();
    }
}
