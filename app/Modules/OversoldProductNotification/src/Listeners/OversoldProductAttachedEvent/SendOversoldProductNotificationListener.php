<?php

namespace App\Modules\OversoldProductNotification\src\Listeners\OversoldProductAttachedEvent;

use App\Events\Product\ProductTagAttachedEvent;
use App\Mail\OversoldProductMail;
use App\Models\MailTemplate;
use Mail;

class SendOversoldProductNotificationListener
{
    public function handle(ProductTagAttachedEvent $event)
    {
        if ($event->tag() != 'oversold') {
            return;
        }

        $template = new OversoldProductMail([
            'product' => $event->product()->toArray(),
            'tag' => $event->tag(),
        ]);

        $mailTemplate = MailTemplate::select('to')
            ->where('mailable', 'App\Mail\OversoldProductMail')
            ->first();

        if ($mailTemplate->to) {
            Mail::to($mailTemplate->to)->send($template);
        }
    }
}
