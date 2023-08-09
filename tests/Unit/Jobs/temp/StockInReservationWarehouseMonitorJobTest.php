<?php

namespace Tests\Unit\Jobs\temp;

use App\Jobs\DispatchEveryDayEventJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsEventServiceProviderBase;
use App\Modules\InventoryReservations\src\Models\Configuration;
use App\Modules\Maintenance\src\EventServiceProviderBase as MaintenanceEventServiceProviderBase;
use Tests\TestCase;

class StockInReservationWarehouseMonitorJobTest extends TestCase
{
    public function testIfFixesQuantityReserved()
    {
        MaintenanceEventServiceProviderBase::enableModule();
        InventoryReservationsEventServiceProviderBase::enableModule();

        /** @var Configuration $configuration */
        $configuration = Configuration::first();

        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var Inventory $inventory */
        $inventory = $product->inventory->first;
        $inventory->update(['quantity' => 1]);

        DispatchEveryDayEventJob::dispatch();

        $this->assertNotTrue(
            Inventory::query()
                ->where(['warehouse_id' => $configuration->warehouse_id])
                ->where('quantity_reserved', '>', 0)
                ->exists()
        );

    }
}
