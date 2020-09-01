<?php

namespace App\Listeners\Order\StatusChanged;

use App\Events\Order\StatusChangedEvent;
use App\Models\PickRequest;

class DeletePickRequestsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param StatusChangedEvent $event
     * @return void
     */
    public function handle(StatusChangedEvent $event)
    {
        if ($event->isNotStatusCode('picking')) {
            $this->deletePickRequests($event);
        }
    }

    /**
     * @param StatusChangedEvent $event
     */
    private function deletePickRequests(StatusChangedEvent $event): void
    {
        $orderProducts = $event->getOrder()->orderProducts()->get();

        foreach ($orderProducts as $orderProduct) {
            $pickRequests = PickRequest::query()
                ->where([
                    'order_product_id' => $orderProduct->getKey(),
                    'quantity_picked' => 0,
                ])
                ->whereNull('deleted_at')
                ->get();

            foreach ($pickRequests as $pickRequest) {
                $pickRequest->delete();
            }
        }
    }
}
