<?php


namespace Tests\Feature\Modules\ShipmentConfirmationEmail;

use App\Mail\ShipmentConfirmationMail;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderShipment;
use App\Modules\ShipmentConfirmationEmail\src\ServiceProvider;
use Mail;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function test_if_sends_email()
    {
        Mail::fake();

        ServiceProvider::enableModule();

        /** @var OrderAddress $address */
        $address = factory(OrderAddress::class)->make();
        $address->save();

        /** @var Order $order */
        $order = factory(Order::class)->make();
        $order->shippingAddress()->associate($address);
        $order->save();

        /** @var OrderShipment $shipment */
        $shipment = factory(OrderShipment::class)->make();
        $shipment->order()->associate($order);
        $shipment->save();

        Mail::assertSent(ShipmentConfirmationMail::class, function ($mail) use ($address) {
            return $mail->hasTo($address->email);
        });
    }

    public function test_if_does_not_sends_when_email_missing()
    {
        Mail::fake();

        ServiceProvider::enableModule();

        /** @var OrderAddress $address */
        $address = factory(OrderAddress::class)->make(['email' => '']);
        $address->save();

        /** @var Order $order */
        $order = factory(Order::class)->make();
        $order->shippingAddress()->associate($address);
        $order->save();

        /** @var OrderShipment $shipment */
        $shipment = factory(OrderShipment::class)->make();
        $shipment->order()->associate($order);
        $shipment->save();

        Mail::assertNotSent(ShipmentConfirmationMail::class, function ($mail) use ($address) {
            return $mail->hasTo($address->email);
        });
    }
}
