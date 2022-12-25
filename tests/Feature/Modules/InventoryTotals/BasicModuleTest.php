<?php

namespace Tests\Feature\Modules\InventoryTotals;

use App\Models\Inventory;
use App\Models\InventoryTotal;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryTotals\src\InventoryTotalsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

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
