<?php

namespace App\Modules\Webhooks\src\Listeners\SyncRequestedEvent;

use App\Events\Product\ProductUpdatedEvent;
use App\Modules\Webhooks\src\Jobs\PublishOrdersWebhooksJob;

class PublishOrdersWebhooksListener
{
    /**
     * Handle the event.
     *
     * @param ProductUpdatedEvent $event
     * @return void
     */
    public function handle(ProductUpdatedEvent $event)
    {
        PublishOrdersWebhooksJob::dispatch();
    }
}
