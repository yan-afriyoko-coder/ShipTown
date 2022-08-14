<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Modules\Webhooks\src\Jobs\PublishInventoryWebhooksJob;
use App\Modules\Webhooks\src\Models\PendingWebhook;

class InventoryUpdatedEventListener
{
    public function handle(InventoryUpdatedEvent $event)
    {
        PendingWebhook::query()->updateOrCreate([
            'model_class' => Inventory::class,
            'model_id' => $event->inventory->getKey(),
            'reserved_at' => null,
            'published_at' => null,
        ], [
            'message' => InventoryResource::make($event->inventory->load(['product', 'warehouse']))->toJson(),
        ]);

        PublishInventoryWebhooksJob::dispatchAfterResponse();
    }
}
