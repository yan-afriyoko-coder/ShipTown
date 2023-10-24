<?php

namespace Tests\Feature\Modules\InventoryMovements;

use App\Events\EveryDayEvent;
use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryHourEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
use App\Events\SyncRequestedEvent;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryMovements\src\InventoryMovementsServiceProvider;
use App\Modules\InventoryMovements\src\Jobs\InventoryLastMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\InventoryQuantityJob;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityAfterJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeBasicJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityDeltaJob;
use App\Modules\InventoryMovements\src\Models\Configuration;
use App\Services\InventoryService;
use Tests\TestCase;

class QuantityBeforeTest extends TestCase
{
    private InventoryMovement $inventoryMovement01;
    private InventoryMovement $inventoryMovement02;
    private float $initialQuantity;
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

        $this->assertNotNull($inventoryMovement02->previous_movement_id, 'previous_movement_id should not be null');
        $this->assertEquals($inventoryMovement01->getKey(), $inventoryMovement02->previous_movement_id, 'Not matching previous_movement_id');
        $this->assertEquals($inventoryMovement02->getKey(), $inventoryMovement03->previous_movement_id, 'Not matching previous_movement_id');

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
