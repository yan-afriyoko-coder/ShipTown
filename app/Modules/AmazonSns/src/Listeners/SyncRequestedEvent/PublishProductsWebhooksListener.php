<?php

namespace App\Modules\AmazonSns\src\Listeners\SyncRequestedEvent;

use App\Events\SyncRequestedEvent;
use App\Modules\AmazonSns\src\Jobs\PublishProductsWebhooksJob;

class PublishProductsWebhooksListener
{
    /**
     * Handle the event.
     *
     * @param SyncRequestedEvent $event
     * @return void
     */
    public function handle(SyncRequestedEvent $event)
    {
        PublishProductsWebhooksJob::dispatch();
    }
}
