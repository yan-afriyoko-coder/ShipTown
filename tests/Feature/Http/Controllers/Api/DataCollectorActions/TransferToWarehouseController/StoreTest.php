<?php

namespace Tests\Feature\Http\Controllers\Api\DataCollectorActions\TransferToWarehouseController;

use App\Models\DataCollection;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;
use function factory;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        $dataCollection = factory(DataCollection::class)->create();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')
            ->postJson(route('api.data-collector-actions.transfer-to-warehouse.store'), [
                'data_collector_id' => $dataCollection->getKey(),
                'destination_warehouse_id' => factory(Warehouse::class)->create()->getKey(),
            ]);

        ray($response->json());

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'id'
            ],
        ]);
    }
}
