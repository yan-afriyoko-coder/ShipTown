<?php

namespace Tests\Feature\Http\Controllers\Api\DataCollectorController;

use App\Models\DataCollection;
use App\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    /** @test */
    public function test_destroy_call_returns_ok()
    {
        $user = User::factory()->create();

        $dataCollector = DataCollection::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->delete(route('data-collector.destroy', $dataCollector->getKey()));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id'
            ],
        ]);
    }
}
