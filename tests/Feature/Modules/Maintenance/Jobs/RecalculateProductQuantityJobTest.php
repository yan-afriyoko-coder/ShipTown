<?php

namespace Tests\Feature\Modules\Maintenance\Jobs;

use App\Jobs\RunHourlyJobs;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Tests\TestCase;

class RecalculateProductQuantityJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Product::query()->forceDelete();
        Order::query()->forceDelete();
        OrderProduct::query()->forceDelete();

        Order::factory()
            ->with('orderProducts', 2)
            ->create(['status_code' => 'paid']);

        Product::query()->update([
            'quantity' => 0,
        ]);

        RunHourlyJobs::dispatchNow();

        $this->assertEquals(
            Product::query()->sum('quantity'),
            Inventory::query()->sum('quantity')
        );
    }
}
