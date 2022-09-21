<?php

namespace Tests\Feature\Http\Controllers\Api\CsvImport\DataCollectionsImportController;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        /** @var Product $product */
        $product = factory(Product::class)->create();

        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->postJson(route('csv-import-data-collections.store'), [
            'data_collection_name_prefix' => 'test',
            'data' => [
                [
                    'product_sku' => $product->sku,
                    $warehouse->code => rand(0, 100),
                ],
            ]
        ]);

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'success',
            ],
        ]);
    }
}
