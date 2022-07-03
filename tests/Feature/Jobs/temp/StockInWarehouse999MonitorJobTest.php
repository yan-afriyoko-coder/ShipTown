<?php

namespace Tests\Feature\Jobs\temp;

use App\Events\HourlyEvent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Maintenance\src\EventServiceProviderBase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockInWarehouse999MonitorJobTest extends TestCase
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

        factory(Warehouse::class)->firstOrCreate(['code' => '999']);

        /** @var Product $product */
        $product = factory(Product::class)->create();

        $inventory = $product->inventory->first;
        $inventory->update(['quantity' => 1]);

        HourlyEvent::dispatch();

        $this->assertNotTrue(
            Inventory::query()
                ->where(['warehouse_code' => '999'])
                ->where('quantity', '>', 0)
                ->exists()
        );
    }
}
