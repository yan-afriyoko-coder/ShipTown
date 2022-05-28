<?php

namespace App\Modules\Automations\src\Actions;

use App\Mail\OrderMail;
use App\Models\MailTemplate;
use App\Modules\Automations\src\Abstracts\BaseOrderActionAbstract;
use Illuminate\Support\Facades\Mail;

/**
 *
 */
class SendOrderEmailActionAbstract extends BaseOrderActionAbstract
{
    /**
     * @param string $options
     * @return bool
     */
    public function handle(string $options = ''): bool
    {
        parent::handle($options);

        /** @var MailTemplate $template */
        $template = MailTemplate::query()
            ->where('code', '<>', '')
            ->where(['code' => $options])
            ->first();

        $mailable = new OrderMail($template, [
            'order' => $this->order->toArray(),
            'shipments' => $this->order->orderShipments->toArray(),
        ]);

        Mail::to($this->order->shippingAddress->email)
            ->send($mailable);

        activity()->on($this->order)
            ->causedByAnonymous()
            ->withProperties(['template_code' => $template->code])
            ->log('Email was send');

        return true;
    }
}
