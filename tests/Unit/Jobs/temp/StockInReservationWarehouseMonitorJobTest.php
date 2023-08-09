<?php

namespace Tests\Unit\Jobs\temp;

use App\Jobs\DispatchEveryDayEventJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsEventServiceProviderBase;
use App\Modules\InventoryReservations\src\Models\Configuration;
use App\Modules\Maintenance\src\EventServiceProviderBase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockInReservationWarehouseMonitorJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        EventServiceProviderBase::enableModule();
        InventoryReservationsEventServiceProviderBase::enableModule();

        $inventoryReservationsWarehouseId = Configuration::first()->warehouse_id;

        /** @var Product $product */
        $product = Product::factory()->create();

        $inventory = $product->inventory->first;
        $inventory->update(['quantity' => 1]);

        DispatchEveryDayEventJob::dispatch();

        $this->assertNotTrue(
            Inventory::query()
                ->where(['warehouse_id' => $inventoryReservationsWarehouseId])
                ->where('quantity', '>', 0)
                ->exists()
        );
    }
}
