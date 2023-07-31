<?php

namespace Tests\Feature\Modules\InventoryMovementsStatistics;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryMovementsStatistics\src\InventoryMovementsStatisticsServiceProvider;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RemoveOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Models\InventoryMovementsStatistic;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_if_sales_are_removed_from_statistics()
    {
        InventoryMovementsStatisticsServiceProvider::enableModule();

        Product::factory()->create();
        Warehouse::factory()->create();

        /** @var Inventory $inventory */
        $inventory = Inventory::first();

        $randomQuantity = rand(1, 100);

        InventoryMovement::query()->create([
            'custom_unique_reference_id' => 'test',
            'type' => 'sale',
            'inventory_id' => $inventory->id,
            'product_id' => $inventory->product_id,
            'warehouse_id' => $inventory->warehouse_id,
            'quantity_before' => $inventory->quantity + $randomQuantity * -1,
            'quantity_delta' => $randomQuantity * -1,
            'quantity_after' => $inventory->quantity + $randomQuantity * -1,
            'description' => 'test',
        ]);

        $created_at = now()->subDays(30);
        InventoryMovement::query()->update(['created_at' => $created_at]);
        DB::statement('UPDATE inventory_movements_statistics SET created_at = ?', [$created_at]);

        /** @var InventoryMovementsStatistic $inventoryMovementStatistic */
        $inventoryMovementStatistic = InventoryMovementsStatistic::query()
            ->where('inventory_id', $inventory->id)
            ->first();

        ray($inventoryMovementStatistic);

        $this->assertNotNull($inventoryMovementStatistic);

        ray()->showQueries();
        RemoveOutdatedSalesJob::dispatchSync();
        RemoveOutdatedSalesJob::dispatchSync();

//        dd(1);
//        Remove14DaysOutdatedSalesJob::dispatchSync();
//        Remove28DaysOutdatedSalesJob::dispatchSync();
//
        $inventoryMovementStatistic->refresh();

        $this->assertEquals(0, $inventoryMovementStatistic->last7days_quantity_delta, '7 days');
        $this->assertEquals(0, $inventoryMovementStatistic->last14days_quantity_delta, '14 days');
        $this->assertEquals(0, $inventoryMovementStatistic->last28days_quantity_delta, '28 days');

        // we just make sure jobs run without errors
        $this->assertTrue(true);
    }

    /** @test */
    public function test_if_sales_are_added_to_statistics()
    {
        InventoryMovementsStatisticsServiceProvider::enableModule();

        Product::factory()->create();
        Warehouse::factory()->create();

        /** @var Inventory $inventory */
        $inventory = Inventory::first();

        $quantitySold = rand(1, 100);
        InventoryService::sellProduct($inventory, $quantitySold, 'test');

        /** @var InventoryMovementsStatistic $inventoryMovementStatistic */
        $inventoryMovementStatistic = InventoryMovementsStatistic::query()
            ->where('inventory_id', $inventory->id)
            ->first();

        $this->assertNotNull($inventoryMovementStatistic);

        $this->assertEquals($quantitySold, $inventoryMovementStatistic->last7days_quantity_delta);
        $this->assertEquals($quantitySold, $inventoryMovementStatistic->last14days_quantity_delta);
        $this->assertEquals($quantitySold, $inventoryMovementStatistic->last28days_quantity_delta);

        // we just make sure jobs run without errors
        $this->assertTrue(true);
    }

    /** @test */
    public function test_module_basic_functionality()
    {
        InventoryMovementsStatisticsServiceProvider::enableModule();

        Product::factory()->create();
        Warehouse::factory()->create();

        /** @var Inventory $inventory */
        $inventory = Inventory::first();

        $randomQuantity = rand(1, 100);

        InventoryMovement::query()->create([
            'custom_unique_reference_id' => 'test',
            'type' => 'sale',
            'inventory_id' => $inventory->id,
            'product_id' => $inventory->product_id,
            'warehouse_id' => $inventory->warehouse_id,
            'quantity_before' => $inventory->quantity + $randomQuantity * -1,
            'quantity_delta' => $randomQuantity * -1,
            'quantity_after' => $inventory->quantity + $randomQuantity * -1,
            'description' => 'test',
        ]);

        RemoveOutdatedSalesJob::dispatch();

        // we just make sure jobs run without errors
        $this->assertTrue(true);
    }
}
