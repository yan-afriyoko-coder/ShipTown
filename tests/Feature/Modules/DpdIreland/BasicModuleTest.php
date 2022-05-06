<?php

namespace Tests\Feature\Modules\DpdIreland;

use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\Modules\DpdIreland\src\Services\NextDayShippingService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $preAdvice = new PreAdvice(/** @lang text */
            '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
                <PreadviceResponse>
                    <Status>OK</Status>
                    <PreAdviceErrorCode></PreAdviceErrorCode>
                    <PreAdviceErrorDetails></PreAdviceErrorDetails>
                    <ReceivedConsignmentsNumber>1</ReceivedConsignmentsNumber>
                    <Consignment>
                        <RecordID>1</RecordID>
                        <TrackingNumber>999999999</TrackingNumber>
                        <DeliveryDepot>44</DeliveryDepot>
                    </Consignment>
                </PreadviceResponse>
            '
        );

        Mockery::mock('alias:'.Dpd::class)
            ->shouldReceive('shipOrder')
            ->withAnyArgs()
            ->andReturn($preAdvice);
    }

    /** @test
     * @throws GuzzleException
     */
    public function test_module_basic_functionality()
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();

        $shippingService = new NextDayShippingService();
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
