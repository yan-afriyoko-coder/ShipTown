<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\Every10minEvent;
use App\Modules\Webhooks\src\Jobs\PublishAllWebhooksJob;

class Every10minEventListener
{
    public function handle(Every10minEvent $event)
    {
        PublishAllWebhooksJob::dispatch();
    }
}
