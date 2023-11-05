<?php

namespace Tests\Feature\Modules\InventoryMovements;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryMovements\src\InventoryMovementsServiceProvider;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Models\Configuration;
use App\Services\InventoryService;
use Tests\TestCase;

class PreviousMovementIdJobTest extends TestCase
{
    private Inventory $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        InventoryMovementsServiceProvider::enableModule();

        /** @var Product $product */
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $this->inventory = Inventory::find($product->getKey(), $warehouse->getKey());
    }

    public function testBasicScenario()
    {
        $inventoryMovement01 = InventoryService::adjust($this->inventory, 20);
        $inventoryMovement02 = InventoryService::sell($this->inventory, -5);
        $inventoryMovement03 = InventoryService::adjust($this->inventory, 7);

        PreviousMovementIdJob::dispatch();

        $inventoryMovement01->refresh();
        $inventoryMovement02->refresh();
        $inventoryMovement03->refresh();

        ray(InventoryMovement::query()->get()->toArray(), Configuration::query()->firstOrCreate([])->toArray());

        $this->assertNotNull($inventoryMovement02->previous_movement_id, '$inventoryMovement02 previous_movement_id should not be null');
        $this->assertEquals($inventoryMovement01->getKey(), $inventoryMovement02->previous_movement_id, '$inventoryMovement01 Not matching previous_movement_id');
        $this->assertEquals($inventoryMovement02->getKey(), $inventoryMovement03->previous_movement_id, '$inventoryMovement02 Not matching previous_movement_id');

        $this->assertDatabaseHas('inventory_movements', [
            'id' => $inventoryMovement02->getKey(),
            'quantity_before' => $inventoryMovement01->quantity_after,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'id' => $inventoryMovement03->getKey(),
            'quantity_before' => $inventoryMovement02->quantity_after,
        ]);
    }
}
