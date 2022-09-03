<?php

namespace Tests\Feature\Http\Controllers\Api\DataCollectorController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->postJson(route('data-collector.store'), [
            'name' => 'test',
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'created_at',
                'updated_at',
            ],
        ]);
    }
}
