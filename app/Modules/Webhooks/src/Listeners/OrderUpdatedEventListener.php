<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\Webhooks\src\Models\PendingWebhook;

/**
 * Class AttachAwaitingPublishTagListener.
 */
class OrderUpdatedEventListener
{
    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     *
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        PendingWebhook::query()->firstOrCreate([
            'model_class' => Order::class,
            'model_id' => $event->order->getKey(),
            'reserved_at' => null,
            'published_at' => null,
        ]);
    }
}
