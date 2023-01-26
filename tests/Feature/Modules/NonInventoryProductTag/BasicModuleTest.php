<?php

namespace Tests\Feature\Modules\NonInventoryProductTag;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\NonInventoryProductTag\src\Jobs\ClearArcadiaStockJob;
use App\Modules\NonInventoryProductTag\src\NonInventoryProductTagServiceProvider;
use App\Services\InventoryService;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
//    /** @test */
//    public function test_module_basic_functionality()
//    {
//        NonInventoryProductTagServiceProvider::enableModule();
//
//        $warehouse = Warehouse::factory()->create();
//        $product = Product::factory()->create();
//
//        $inventory = Inventory::firstOrCreate([
//            'product_id' => $product->id,
//            'warehouse_id' => $warehouse->id,
//        ], []);
//
//        $inventory->product->attachTag('non-inventory');
//
//        InventoryService::adjustQuantity($inventory, 10, 'test');
//
//        $inventory->refresh();
//
//        $this->assertEquals(0, $inventory->quantity);
//    }

    public function test_basic()
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        /** @var Inventory $inventory */
        $inventory = Inventory::query()
            ->where([
                'product_id' => $product->getKey(),
                'warehouse_id' => $warehouse->getKey(),
            ])
            ->first();

        InventoryService::adjustQuantity($inventory, 10, 'test');

        $product->attachTag('ARCADIA DEAL JAN 2023', 'rms_sub_description3');

        ClearArcadiaStockJob::dispatch();

        $inventory1 = Inventory::query()->where('quantity', '!=', 0);

        $this->assertFalse($inventory1->exists());
    }
}
