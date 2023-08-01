<?php

namespace Tests\Feature\Modules\OrderTotals;

use App\Events\EveryDayEvent;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductTotal;
use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsEventServiceProviderBase;
use App\Modules\OrderTotals\src\Jobs\EnsureAllRecordsExistsJob;
use App\Modules\OrderTotals\src\Jobs\EnsureCorrectTotalsJob;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class EnsureCorrectTotalsJobTest extends TestCase
{
    public function test_if_dispatches_job()
    {
        InventoryReservationsEventServiceProviderBase::enableModule();

        Bus::fake(EnsureCorrectTotalsJob::class);

        EveryDayEvent::dispatch();

        Bus::assertDispatched(EnsureCorrectTotalsJob::class);
    }

    public function test_if_updates_totals()
    {
        InventoryReservationsEventServiceProviderBase::enableModule();
        // Create Order with order product
        /** @var OrderProduct $orderProduct */
        $orderProduct = OrderProduct::factory()->create();
        $orderProduct = $orderProduct->refresh();

        // create order without any orderProduct
        Order::factory()->create();

        OrderProductTotal::query()->forceDelete();

        EnsureAllRecordsExistsJob::dispatchSync();

        $this->assertDatabaseCount('orders_products_totals', 2);

        EnsureCorrectTotalsJob::dispatchSync();

        $this->assertDatabaseHas('orders_products_totals', ['order_id' => $orderProduct->order_id]);
        $this->assertDatabaseHas('orders_products_totals', ['count' => 1]);
        $this->assertDatabaseHas('orders_products_totals', ['quantity_ordered' => $orderProduct->quantity_ordered]);
        $this->assertDatabaseHas('orders_products_totals', ['quantity_split' => $orderProduct->quantity_split]);
        $this->assertDatabaseHas('orders_products_totals', ['quantity_picked' => $orderProduct->quantity_picked]);
        $this->assertDatabaseHas('orders_products_totals', ['quantity_skipped_picking' => $orderProduct->quantity_skipped_picking]);
        $this->assertDatabaseHas('orders_products_totals', ['quantity_not_picked' => $orderProduct->quantity_not_picked]);
        $this->assertDatabaseHas('orders_products_totals', ['quantity_shipped' => $orderProduct->quantity_shipped]);
    }
}
