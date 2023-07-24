<?php

namespace Tests\Feature\Modules\InventoryMovementsStatistics;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryMovementsStatistics\src\InventoryMovementsStatisticsServiceProvider;
use App\Modules\InventoryMovementsStatistics\src\Jobs\AccountForNewSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove14DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove28DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove7DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Models\InventoryMovementsStatistic;
use App\Services\InventoryService;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_if_sales_are_added_to_statistics()
    {
        InventoryMovementsStatisticsServiceProvider::enableModule();

        Product::factory()->create();
        Warehouse::factory()->create();

        /** @var Inventory $inventory */
        $inventory = Inventory::first();

        $quantitySold = rand(1, 100);
        InventoryService::sellProduct($inventory, $quantitySold * -1, 'test');

        /** @var InventoryMovementsStatistic $inventoryMovementStatistic */
        $inventoryMovementStatistic = InventoryMovementsStatistic::query()
            ->where('inventory_id', $inventory->id)
            ->first();

        $this->assertNotNull($inventoryMovementStatistic);

        $this->assertEquals($quantitySold, $inventoryMovementStatistic->quantity_sold_last_7_days);
        $this->assertEquals($quantitySold, $inventoryMovementStatistic->quantity_sold_last_14_days);
        $this->assertEquals($quantitySold, $inventoryMovementStatistic->quantity_sold_last_28_days);

        // we just make sure jobs run without errors
        $this->assertTrue(true);
    }

    /** @test */
    public function test_module_basic_functionality()
    {
        InventoryMovementsStatisticsServiceProvider::enableModule();

        AccountForNewSalesJob::dispatch();
        Remove7DaysOutdatedSalesJob::dispatch();
        Remove14DaysOutdatedSalesJob::dispatch();
        Remove28DaysOutdatedSalesJob::dispatch();

        // we just make sure jobs run without errors
        $this->assertTrue(true);
    }
}
