<?php

namespace Tests\Feature\Modules\ScurriAnpost;

use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\ScurriAnpost\src\Scurri;
use App\Modules\ScurriAnpost\src\ScurriServiceProvider;
use App\Modules\ScurriAnpost\src\Services\AnPostShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_module_basic_functionality()
    {
        ScurriServiceProvider::enableModule();

        /** @var Order $order */
        $order = Order::factory()->create();

        Mockery::mock('alias:'.Scurri::class)
            ->shouldReceive('createShippingLabel')
            ->withAnyArgs()
            ->andReturn(new ShippingLabel([
                'order_id' => $order->id,
                'shipping_number' => '123456',
            ]));

        $shippingService = new AnPostShippingService();
        $shippingLabelCollection = $shippingService->ship($order->getKey());

        $this->assertCount(1, $shippingLabelCollection);
    }
}
