<?php

namespace App\Listeners\Order\StatusChanged;

use App\Events\Order\StatusChangedEvent;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PackingStatusesRules
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
        // todo change hardcoded
        $expectedStatusCode = 'paid';

        if ($event->isNotStatusCode($expectedStatusCode)) {
            return;
        }

        $this->checkStatusAndUpdate($event->getOrder());
    }

    /**
     * @param Order $order
     */
    public function checkStatusAndUpdate(Order $order): void
    {
        // todo change hardcoded
        $sourceLocationId = 99;
        $newStatusCode = 'packing_warehouse';

        if (OrderService::canFulfill($order, $sourceLocationId)) {
            $this->updateStatusWithLog($order, $newStatusCode, $sourceLocationId);

            return;
        }

        if (OrderService::canNotFulfill($order)) {
            $this->updateStatusWithLog($order, 'auto_missing_item', 0);
            return;
        }
    }

    /**
     * @param Order $order
     * @param string $newStatusCode
     * @param int $sourceLocationId
     */
    private function updateStatusWithLog(Order $order, string $newStatusCode, int $sourceLocationId): void
    {
        $order->update(['status_code' => $newStatusCode]);

        // Log event
        info(
            'PackingStatusesRules: set status to:',
            [
                'order_number' => $order->order_number,
                'nwe_status_code' => $newStatusCode,
                'source_location_id' => $sourceLocationId,
            ]
        );
    }
}
