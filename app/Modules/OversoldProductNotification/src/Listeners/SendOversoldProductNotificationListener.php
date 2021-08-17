<?php

namespace App\Modules\OversoldProductNotification\src\Listeners;

use App\Events\Product\ProductTagAttachedEvent;
use App\Mail\OversoldProductMail;
use App\Models\MailTemplate;
use App\Modules\OversoldProductNotification\src\Jobs\SendOversoldProductMailJob;
use Mail;

class SendOversoldProductNotificationListener
{
    public function handle(ProductTagAttachedEvent $event)
    {
        if ($event->tag() != 'oversold') {
            return;
        }

        $data = [
            'product' => $event->product()->toArray(),
            'tag' => $event->tag(),
        ];

        $mailTemplate = MailTemplate::select('to')
            ->where('mailable', 'App\Mail\OversoldProductMail')
            ->first();

        $to = $mailTemplate->to;

        SendOversoldProductMailJob::dispatch($data, $to)->onConnection('database');
    }
}
