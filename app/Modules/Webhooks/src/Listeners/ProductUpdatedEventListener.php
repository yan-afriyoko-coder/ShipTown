<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\Product\ProductUpdatedEvent;
use App\Models\Product;
use App\Modules\Webhooks\src\Models\PendingWebhook;

class ProductUpdatedEventListener
{
    /**
     * Handle the event.
     *
     * @param ProductUpdatedEvent $event
     *
     * @return void
     */
    public function handle(ProductUpdatedEvent $event)
    {
        PendingWebhook::query()->firstOrCreate([
            'model_class' => Product::class,
            'model_id' => $event->product->getKey(),
            'reserved_at' => null,
            'published_at' => null,
        ]);
    }
}
