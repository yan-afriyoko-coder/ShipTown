<?php

namespace Tests\Feature\Http\Controllers\Api\DataCollectorRecordController;

use App\Models\DataCollection;
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

        $dataCollection = factory(DataCollection::class)->create(['name' => 'test']);

        factory(DataCollectionRecord::class)->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => factory(Product::class)->create()->getKey(),
            'quantity_scanned' => rand(1, 10),
        ]);

        $response = $this->actingAs($user, 'api')->getJson(route('data-collector-records.index'));

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
