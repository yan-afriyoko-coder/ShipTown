<?php

namespace Tests\Feature\Http\Controllers\Api\HeartbeatsController;

use App\Models\Heartbeat;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');
    }

    /** @test */
    public function test_index_call_returns_ok()
    {
        // add expired heartbeatgc
        Heartbeat::updateOrCreate([
                'code' => 'somealert'
            ],
            [
                'expired_at' => now()->subMinutes(10),
                'error_message' => 'Some error message'
            ]
        );

        $response = $this->get(route('heartbeats.index'));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'code',
                    'expired_at',
                    'error_message'
                ],
            ],
        ]);
        $response->assertJsonFragment(['code' => 'somealert']);
    }
}
