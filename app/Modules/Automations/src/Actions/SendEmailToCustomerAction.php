<?php

namespace App\Modules\Automations\src\Actions;

use App\Mail\ShipmentConfirmationMail;
use App\Modules\Automations\src\BaseOrderAction;
use Illuminate\Support\Facades\Mail;

class SendEmailToCustomerAction extends BaseOrderAction
{
    public function handle($options = '')
    {
        parent::handle($options);

        $template = new ShipmentConfirmationMail([
            'order' => $this->order->toArray(),
            'shipments' => $this->order->orderShipments->toArray(),
        ]);

        Mail::to($this->order->shippingAddress->email)
            ->send($template);
    }
}
