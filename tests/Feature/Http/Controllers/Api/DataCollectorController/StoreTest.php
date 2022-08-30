<?php

namespace Tests\Feature\Http\Controllers\Api\DataCollectorController;

use App\Models\Product;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->postJson(route('data-collector.store'), [
            'product_id'=> factory(Product::class)->create()->getKey(),
            'quantity_collected' => rand(0, 100),
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $this->assertCount(1, $response->json('data'), 'No records returned');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'product_id',
                    'quantity_collected'
                ],
            ],
        ]);
    }
}
