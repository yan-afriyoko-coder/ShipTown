<?php

namespace Tests\Feature\Modules\InventoryQuantityIncoming;

use App\Models\DataCollection;
use App\Models\DataCollectionTransferIn;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryQuantityIncoming\src\InventoryQuantityIncomingServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function test_basic_functionality()
    {
        InventoryQuantityIncomingServiceProvider::enableModule();

        $warehouse = factory(Warehouse::class)->create();
        $product = factory(Product::class)->create();

        $inventory = Inventory::query()->where([
            'warehouse_id' => $warehouse->getKey(),
            'product_id' => $product->getKey(),
        ])->first();

        $inventory->update([
            'quantity' => 10,
            'restock_level' => 0,
            'reorder_point' => 0,
        ]);

        /** @var DataCollection $dataCollection */
        $dataCollection = factory(DataCollection::class)->create([
            'type' => DataCollectionTransferIn::class,
            'name' => 'Test',
            'warehouse_id' => $warehouse->getKey(),
        ]);

        $dataCollection->records()->create([
            'product_id' => $inventory->product_id,
            'quantity_requested' => 10,
        ]);

        $this->assertEquals(10, $inventory->fresh()->quantity_incoming);
    }
}
