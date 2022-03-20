<?php

namespace App\Modules\Automations\src\Actions;

use App\Mail\OrderMail;
use App\Models\MailTemplate;
use App\Modules\Automations\src\BaseOrderAction;
use Illuminate\Support\Facades\Mail;

/**
 *
 */
class SendOrderEmailAction extends BaseOrderAction
{
    /**
     * @param string $options
     * @return bool
     */
    public function handle(string $options = ''): bool
    {
        parent::handle($options);

        if (empty($options)) {
            return false;
        }

        $savedMailTemplate = MailTemplate::where(['code' => $options])->first();

        if (is_null($savedMailTemplate)) {
            return false;
        }

        $mailable = new OrderMail($savedMailTemplate, [
            'order' => $this->order->toArray(),
            'shipments' => $this->order->orderShipments->toArray(),
        ]);

        Mail::to($this->order->shippingAddress->email)
            ->send($mailable);

        return true;
    }
}
