<?php

namespace Tests\Feature\Modules\NonInventoryProductTag;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\NonInventoryProductTag\src\NonInventoryProductTagServiceProvider;
use App\Services\InventoryService;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        NonInventoryProductTagServiceProvider::enableModule();

        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();

        $inventory = Inventory::firstOrCreate([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
        ], []);

        $inventory->product->attachTag('non-inventory');

        InventoryService::adjustQuantity($inventory, 10, 'test');

        $inventory->refresh();

        $this->assertEquals(0, $inventory->quantity);
    }
}
