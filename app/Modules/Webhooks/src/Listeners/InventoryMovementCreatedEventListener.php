<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\InventoryMovementCreatedEvent;
use App\Models\InventoryMovement;
use App\Modules\Webhooks\src\Jobs\PublishInventoryMovementWebhooksJob;
use App\Modules\Webhooks\src\Models\PendingWebhook;

class InventoryMovementCreatedEventListener
{
    public function handle(InventoryMovementCreatedEvent $event)
    {
        PendingWebhook::query()->firstOrCreate([
            'model_class' => InventoryMovement::class,
            'model_id' => $event->inventoryMovement->getKey(),
        ]);

        PublishInventoryMovementWebhooksJob::dispatchAfterResponse();
    }
}
