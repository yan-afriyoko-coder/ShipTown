<?php

namespace Tests\Unit\Modules\InventoryTotals;

use App\Events\Inventory\RecalculateInventoryRequestEvent;
use App\Models\Inventory;
use App\Models\InventoryTotal;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryTotals\src\InventoryTotalsServiceProvider;
use App\Modules\InventoryTotals\src\Jobs\EnsureInventoryTotalsByWarehouseTagRecordsExistJob;
use App\Modules\InventoryTotals\src\Models\InventoryTotalByWarehouseTag;
use App\Modules\InventoryTotals\src\Services\InventoryTotalsService;
use App\Services\InventoryService;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function testIfRecordsAreRecreated()
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();

        $warehouse->attachTag('ALL');

        InventoryTotalByWarehouseTag::query()->forceDelete();

        ray([
            'product' => $product->getKey(),
            'warehouse' => $warehouse->getKey(),
            'tag' => 'ALL'
        ]);

        EnsureInventoryTotalsByWarehouseTagRecordsExistJob::dispatch();

        $this->assertDatabaseHas('inventory_totals_by_warehouse_tag', ['product_id' => $product->getKey()]);
    }

    public function testIfRecordCreatedWhenTagAttachedToWarehouse()
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();

        $warehouse->attachTag('ALL');

        $this->assertDatabaseHas('inventory_totals_by_warehouse_tag', ['product_id' => $product->getKey()]);
    }

    public function test_sales_totals()
    {
        /** @var Inventory $inventory */
        $inventory = Inventory::factory()->create();

        InventoryService::sell($inventory, 1);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $inventory->product_id,
            'quantity_delta' => 1
        ]);
    }

    /** @test */
    public function test_module_basic_functionality()
    {
        InventoryTotalsServiceProvider::enableModule();

        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $randomQuantity = rand(1, 100);

        Inventory::where(['warehouse_id' => $warehouse->getKey()])
            ->first()
            ->update(['quantity' => $randomQuantity]);

        $this->assertDatabaseHas('inventory_totals', [
            'product_id' => $product->getKey(),
            'quantity' => $randomQuantity
        ]);

        $this->assertNotNull(InventoryTotal::where(['product_id' => $product->getKey()])->first()->product);
    }
}
