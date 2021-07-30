<?php

namespace Tests\Feature\Modules\AutoStatusLayaway;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\AutoStatusLayaway\src\AutoStatusLayawayServiceProvider;
use App\Modules\AutoStatusLayaway\src\Jobs\SetLayawayStatusJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoStatusLayawayServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_functionality()
    {
        AutoStatusLayawayServiceProvider::enableModule();

        /** @var Order $order */
        $order = factory(Order::class)->make(['status_code' => 'paid', 'is_active' => true]);
        $order->save();

        factory(OrderProduct::class)->create(['order_id' => $order->getKey()]);

        // module moves order to layaway if can be fulfilled from location_id 1 and has status paid
        $order->orderProducts->each(function (OrderProduct $orderProduct) {
            Inventory::updateOrCreate([
                    'location_id' => 1, // this is hardcoded location_id in module, to be changed
                    'product_id' => $orderProduct->product_id
                ], [
                    'quantity' => 0
                ]);
        });

        SetLayawayStatusJob::dispatchNow($order);

        /** @var Order $order */
        $order = $order->refresh();


        $this->assertEquals('layaway', $order->status_code);
    }
}
