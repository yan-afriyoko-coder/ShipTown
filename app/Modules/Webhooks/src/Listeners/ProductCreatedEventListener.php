<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\Product\ProductCreatedEvent;
use App\Models\Product;
use App\Modules\Webhooks\src\Models\PendingWebhook;

class ProductCreatedEventListener
{
    public function handle(ProductCreatedEvent $event)
    {
        PendingWebhook::query()->firstOrCreate([
            'model_class' => Product::class,
            'model_id' => $event->product->getKey(),
            'reserved_at' => null,
            'published_at' => null,
        ]);
    }
}
