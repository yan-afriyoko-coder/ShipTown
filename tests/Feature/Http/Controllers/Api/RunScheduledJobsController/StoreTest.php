<?php

namespace Tests\Feature\Http\Controllers\Api\RunScheduledJobsController;

use App\Jobs\SyncRequestJob;
use App\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private string $uri = 'api/run-scheduled-jobs';

    /** @test */
    public function testIfCallReturnsOk()
    {
        $user = User::factory()->create()->assignRole('admin');

        Bus::fake([SyncRequestJob::class]);

        $response = $this->actingAs($user, 'api')->postJson($this->uri, ['schedule' => 'SyncRequest']);

        Bus::assertDispatched(SyncRequestJob::class);

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
