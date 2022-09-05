<?php

namespace Tests\Feature\Http\Controllers\Api\CsvImportController;

use App\Models\DataCollection;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_store_call_returns_ok()
    {
        $user = factory(User::class)->create();

        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();

        $dataCollection = factory(DataCollection::class)->create([
            'warehouse_id' => $warehouse->id,
            'name' => 'test'
        ]);

        /** @var Product $product */
        $product = factory(Product::class)->create();

        $response = $this->actingAs($user, 'api')->postJson(route('csv-import.store'), [
            'data_collection_id' => $dataCollection->getKey(),
            'data' => [
                [
                    'product_sku' => $product->sku,
                    'quantity_requested' => rand(0, 100),
                    'quantity_scanned' => rand(0, 100),
                ],
            ]
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'success'
            ],
        ]);
    }
}
