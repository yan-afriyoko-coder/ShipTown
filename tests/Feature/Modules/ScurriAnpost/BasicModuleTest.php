<?php

namespace Tests\Feature\Modules\ScurriAnpost;

use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\DpdIreland\src\Services\NextDayShippingService;
use App\Modules\ScurriAnpost\src\Services\AnPostShippingService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_module_basic_functionality()
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();

        $shippingService = new AnPostShippingService();
        $shippingLabelCollection = $shippingService->ship($order->getKey());

        $this->assertCount(1, $shippingLabelCollection);

        /** @var ShippingLabel $label */
        $label = $shippingLabelCollection->first();

        $this->assertEquals('DPD Ireland', $label->carrier);
        $this->assertEquals('next_day', $label->service);
        $this->assertEquals('999999999', $label->shipping_number);
        $this->assertEquals($order->id, $label->order_id);
        $this->assertNotEmpty($label->base64_pdf_labels);
    }
}
