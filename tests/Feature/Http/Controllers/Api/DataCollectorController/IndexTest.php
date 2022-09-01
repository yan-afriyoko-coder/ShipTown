<?php

namespace Tests\Feature\Http\Controllers\Api\DataCollectorController;

use App\Models\DataCollection;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create();

        factory(DataCollection::class)->create(['name' => 'test']);

        $response = $this->actingAs($user, 'api')->getJson(route('data-collector.index'));

        ray($response->json());

        $response->assertOk();

        $this->assertCount(1, $response->json('data'), 'No records returned');

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id'
                ],
            ],
        ]);
    }
}
