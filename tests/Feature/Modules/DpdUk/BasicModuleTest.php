<?php

namespace Tests\Feature\Modules\DpdUk;

use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\DpdUk\src\Models\Connection;
use App\Modules\DpdUk\src\Services\DpdUkService;
use App\Modules\DpdUk\src\Services\NextDayShippingService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test
     * @throws Exception
     */
    public function test_module_basic_functionality()
    {
        /** Connection */
        $connection = factory(Connection::class)->create();

        /** @var Order $order */
        $order = factory(Order::class)->create();

        $shippingLabel = new ShippingLabel([
            'order_id' => $order->id,
            'shipping_number' => '12345',
            'base64_pdf_labels' => '12345',
        ]);
        $shippingLabel->save();

        Mockery::mock('overload:'.DpdUkService::class)
            ->shouldReceive('createShippingLabel')
            ->withAnyArgs()
            ->andReturn($shippingLabel);

        $shippingService = new NextDayShippingService();
        $shippingLabelCollection = $shippingService->ship($order->getKey());

        $this->assertCount(1, $shippingLabelCollection);

        /** @var ShippingLabel $label */
        $label = $shippingLabelCollection->first();

        $this->assertEquals($order->id, $label->order_id);
        $this->assertNotEmpty($label->base64_pdf_labels, 'missing content');
    }
}
