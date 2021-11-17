<?php


namespace App\Modules\ShipmentConfirmationEmail\src\Listeners\OrderShipmentCreatedEvent;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Mail\ShipmentConfirmationMail;
use Mail;

class SendShipmentConfirmationMailListener
{
    public function handle(OrderShipmentCreatedEvent $event)
    {
        if (empty($event->orderShipment->order->shippingAddress->email)) {
            return;
        }

        if ($event->orderShipment->shipping_number === 'pending') {
            return;
        }

        $template = new ShipmentConfirmationMail([
            'order' => $event->orderShipment->order->toArray(),
            'shipments' => $event->orderShipment->order->orderShipments->toArray(),
        ]);

        Mail::to($event->orderShipment->order->shippingAddress->email)
            ->send($template);
    }
}
