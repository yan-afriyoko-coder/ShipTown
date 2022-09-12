<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\Webhooks\src\Jobs\PublishAllWebhooksJob;
use App\Modules\Webhooks\src\Jobs\PublishInventoryMovementWebhooksJob;

class Every10minEventListener
{
    public function handle(SyncRequestedEvent $event)
    {
        PublishAllWebhooksJob::dispatch();
    }
}
