<?php

namespace Tests\Feature\Modules\InventoryMovements;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryMovements\src\Jobs\InventoryLastMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\InventoryQuantityJob;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityAfterJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityDeltaJob;
use App\Services\InventoryService;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function testBasicFunctionality()
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        /** @var Inventory $inventory */
        $inventory = $product->inventory()->first();
        $initialQuantity = $inventory->quantity;

        $inventoryMovement01 = InventoryService::adjustQuantity($inventory, 20, '');
        $inventoryMovement02 = InventoryService::sellProduct($inventory, -5, '');

        $inventoryMovement02->update([
            'quantity_before' => 100,
        ]);

        PreviousMovementIdJob::dispatch();

        $this->assertDatabaseHas('inventory_movements', [
            'id' => $inventoryMovement02->getKey(),
            'previous_movement_id' => $inventoryMovement01->getKey(),
        ]);

        ray(InventoryMovement::query()->get()->toArray());
        QuantityBeforeJob::dispatch();

        $this->assertDatabaseHas('inventory_movements', [
            'id' => $inventoryMovement02->getKey(),
            'previous_movement_id' => $inventoryMovement01->getKey(),
            'quantity_before' => 20,
            'quantity_delta' => -5,
        ]);

        QuantityDeltaJob::dispatch();
        QuantityAfterJob::dispatch();

        $this->assertDatabaseHas('inventory_movements', [
            'id' => $inventoryMovement02->getKey(),
            'previous_movement_id' => $inventoryMovement01->getKey(),
            'quantity_before' => 20,
            'quantity_delta' => -5,
            'quantity_after' => 15,
        ]);

        InventoryLastMovementIdJob::dispatch();
        InventoryQuantityJob::dispatch();

        $this->assertTrue(true, 'We did not run into any errors');
    }
    /** @test */
    public function testEmptyDatabaseRun()
    {
        PreviousMovementIdJob::dispatch();
        QuantityBeforeJob::dispatch();
        ray(InventoryMovement::query()->get()->toArray());
        QuantityDeltaJob::dispatch();
        QuantityAfterJob::dispatch();
        InventoryLastMovementIdJob::dispatch();
        InventoryQuantityJob::dispatch();

        $this->assertTrue(true, 'We did not run into any errors');
    }
}
