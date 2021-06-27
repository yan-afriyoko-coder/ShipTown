<?php

namespace Tests\Feature\Modules\AutoStatusPackingWarehouse\Jobs;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\AutoStatusPackingWarehouse\src\Jobs\SetStatusPackingWarehouseJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetStatusPackingWarehouseJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var mixed
     */
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->order = factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'paid']);


        // make sure has inventory in location 99
        $this->order->orderProducts->each(function (OrderProduct $orderProduct) {
            Inventory::updateOrCreate([
                'location_id' => 99,
                'product_id' => $orderProduct->product_id
            ],[
                'quantity' => $orderProduct->quantity_ordered,
                'quantity_reserved' => 0
            ]);
        });
    }

    /**
     * @return void
     */
    public function test_if_changes_status_from_paid()
    {
        $this->order->status_code = 'paid';

        SetStatusPackingWarehouseJob::dispatch($this->order);

        $this->order->refresh();

        $this->assertEquals('packing_warehouse', $this->order->status_code);
    }

    /**
     * @return void
     */
    public function test_if_changes_status_from_single_line_orders()
    {
        $this->order->status_code = 'single_line_orders';

        SetStatusPackingWarehouseJob::dispatch($this->order);

        $this->order->refresh();

        $this->assertEquals('packing_warehouse', $this->order->status_code);
    }

    /**
     * @return void
     */
    public function test_if_changes_status_from_packing_web()
    {
        $this->order->status_code = 'packing_web';

        SetStatusPackingWarehouseJob::dispatch($this->order);

        $this->order->refresh();

        $this->assertEquals('packing_warehouse', $this->order->status_code);
    }
}
