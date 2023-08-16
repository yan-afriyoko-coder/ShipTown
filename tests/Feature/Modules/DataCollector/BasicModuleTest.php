<?php

namespace Tests\Feature\Modules\DataCollector;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Warehouse;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function testTransferInScannedAction()
    {
        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::factory()->create();

        DataCollectionRecord::factory()->create(['data_collection_id' => $dataCollection->getKey()]);

        DataCollectorService::runAction($dataCollection, 'transfer_in_scanned');

        $dataCollection->refresh();

        $this->assertNull($dataCollection->currently_running_task);

        $this->assertEmpty(DataCollectionRecord::query()->where('quantity_scanned', '!=', 0)->get());
    }

    public function testTransferOutScannedAction()
    {
        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::factory()->create();

        DataCollectionRecord::factory()->create(['data_collection_id' => $dataCollection->getKey()]);

        DataCollectorService::runAction($dataCollection, 'transfer_out_scanned');

        $dataCollection->refresh();

        $this->assertNull($dataCollection->currently_running_task);
        $this->assertEmpty(DataCollectionRecord::query()->where('quantity_scanned', '!=', 0)->get());
    }

    public function testTransferToScannedAction()
    {
        $sourceWarehouse = Warehouse::factory()->create();
        $destinationWarehouse = Warehouse::factory()->create();

        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::factory()->create([
            'warehouse_id' => $sourceWarehouse->getKey(),
            'destination_warehouse_id' => $destinationWarehouse->getKey(),
        ]);

        DataCollectionRecord::factory()->create(['data_collection_id' => $dataCollection->getKey()]);

        DataCollectorService::runAction($dataCollection, 'transfer_to_scanned');

        $dataCollection->refresh();

        $this->assertNull($dataCollection->currently_running_task);
        $this->assertEmpty(DataCollectionRecord::query()->where('quantity_scanned', '!=', 0)->get());
    }
}
