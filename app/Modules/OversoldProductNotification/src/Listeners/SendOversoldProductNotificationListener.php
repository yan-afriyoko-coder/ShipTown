<?php

namespace App\Modules\OversoldProductNotification\src\Listeners;

use App\Events\Product\ProductTagAttachedEvent;
use App\Models\MailTemplate;
use App\Modules\OversoldProductNotification\src\Jobs\SendOversoldProductMailJob;

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
        if ($mailTemplate->to) {
            $listMail = explode(", ", $mailTemplate->to);
            foreach ($listMail as $mail) {
                if ($mail) {
                    SendOversoldProductMailJob::dispatch($data, $mail);
                }
            }
        }
    }
}
