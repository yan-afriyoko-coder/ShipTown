<?php

namespace Tests\Feature\Jobs\Maintenance;

use App\Jobs\Api2cart\ProcessApi2cartImportedOrderJob;
use App\Jobs\Maintenance\ClearOrderPackerAssignmentJob;
use App\Jobs\Maintenance\UpdateClosedAtIfNullJob;
use App\Models\Order;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\User;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateClosedAtIfNullJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfUpdates()
    {
        Order::query()->forceDelete();
        Api2cartOrderImports::query()->forceDelete();

        $orderImport = factory(Api2cartOrderImports::class)->create();

        ProcessApi2cartImportedOrderJob::dispatchNow($orderImport);

        $order = Order::first();
        $order->update(['order_closed_at' => null]);

        UpdateClosedAtIfNullJob::dispatchNow();

        $order = $order->refresh();
        $this->assertNotNull($order->order_closed_at);
    }
}
