<?php

namespace Tests\Feature\Api\DataCollectorRecords;

use App\Models\DataCollection;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        $user = User::factory()->create();

        $inventory = Inventory::factory()->create();

        $dataCollection = DataCollection::factory()->create([
            'warehouse_id' => $inventory->warehouse_id,
            'name' => 'test'
        ]);

        $response = $this->actingAs($user, 'api')->postJson(route('data-collector-records.store'), [
            'data_collection_id' => $dataCollection->getKey(),
            'inventory_id' => $inventory->getKey(),
            'warehouse_code' => $inventory->warehouse_code,
            'product_id'=> $inventory->product_id,
            'quantity_scanned' => rand(0, 100),
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'product_id',
                'quantity_requested',
                'quantity_to_scan',
                'quantity_scanned',
            ],
        ]);
    }
}
