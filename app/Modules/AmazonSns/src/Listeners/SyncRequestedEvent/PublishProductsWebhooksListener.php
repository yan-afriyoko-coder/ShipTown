<?php

namespace App\Modules\AmazonSns\src\Listeners\SyncRequestedEvent;

use App\Events\Product\ProductUpdatedEvent;
use App\Modules\AmazonSns\src\Jobs\PublishProductsWebhooksJob;

class PublishProductsWebhooksListener
{
    /**
     * Handle the event.
     *
     * @param ProductUpdatedEvent $event
     * @return void
     */
    public function handle(ProductUpdatedEvent $event)
    {
        PublishProductsWebhooksJob::dispatch();
    }
}
