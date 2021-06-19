<?php

namespace App\Modules\AmazonSns\src\Listeners\SyncRequestedEvent;

use App\Events\Product\ProductUpdatedEvent;
use App\Modules\AmazonSns\src\Jobs\PublishOrdersWebhooksJob;

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
