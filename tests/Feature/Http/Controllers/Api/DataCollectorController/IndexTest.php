<?php

namespace Tests\Feature\Http\Controllers\Api\DataCollectorController;

use App\Models\DataCollectionRecord;
use App\Models\Product;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create();

        factory(DataCollectionRecord::class)->create([
            'product_id' => factory(Product::class)->create()->getKey(),
            'quantity' => rand(1, 10),
        ]);

        $response = $this->actingAs($user, 'api')->getJson(route('data-collector.index'));

        ray($response->json());

        $response->assertOk();

        $this->assertEquals(1, $response->json('meta.total'), 'No records returned');

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
