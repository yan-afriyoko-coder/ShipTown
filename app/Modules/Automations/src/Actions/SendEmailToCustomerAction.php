<?php

namespace App\Modules\Automations\src\Actions;

use App\Mail\OrderMail;
use App\Mail\ShipmentConfirmationMail;
use App\Models\MailTemplate;
use App\Modules\Automations\src\BaseOrderAction;
use Illuminate\Support\Facades\Mail;

/**
 *
 */
class SendEmailToCustomerAction extends BaseOrderAction
{
    /**
     * @param string $options
     * @return bool
     */
    public function handle(string $options = ''): bool
    {
        parent::handle($options);

        $template = new ShipmentConfirmationMail([
            'order' => $this->order->toArray(),
            'shipments' => $this->order->orderShipments->toArray(),
        ]);

        Mail::to($this->order->shippingAddress->email)
            ->send($template);

        return true;
    }
}
