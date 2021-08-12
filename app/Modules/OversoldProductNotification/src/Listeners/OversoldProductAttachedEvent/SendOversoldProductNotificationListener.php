<?php

namespace App\Modules\OversoldProductNotification\src\Listeners\OversoldProductAttachedEvent;

use App\Events\Product\ProductTagAttachedEvent;
use App\Mail\OversoldProductMail;
use Mail;

class SendOversoldProductNotificationListener
{
    public function handle(ProductTagAttachedEvent $event)
    {
        $template = new OversoldProductMail([
            'product' => $event->product()->toArray(),
            'tag' => $event->tag(),
        ]);

        Mail::send($template);
    }
}
