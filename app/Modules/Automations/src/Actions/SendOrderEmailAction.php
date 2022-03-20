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

        MailTemplate::query()
            ->where(['code' => $options])
            ->each(function (MailTemplate $template) {
                $mailable = new OrderMail($template, [
                    'order' => $this->order->toArray(),
                    'shipments' => $this->order->orderShipments->toArray(),
                ]);

                Mail::to($this->order->shippingAddress->email)
                    ->send($mailable);

                $this->order->log('Email was send', ['template_code' => $template->code]);
            });

        return true;
    }
}
