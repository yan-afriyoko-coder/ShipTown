<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\Webhooks\src\Jobs\PublishAllWebhooksJob;

class HourlyEventListener
{
    public function handle(HourlyEvent $event)
    {
        PublishAllWebhooksJob::dispatch();
    }
}
