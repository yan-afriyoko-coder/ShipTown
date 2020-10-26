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
        if ($event->isStatusCode('paid')) {
            $this->checkStatusAndUpdate($event->getOrder());
        }
    }

    /**
     * @param Order $order
     */
    public function checkStatusAndUpdate(Order $order): void
    {
//        // todo change hardcoded
//        if (OrderService::canFulfill($order, 99)) {
//            $this->updateStatusWithLog(
//                $order,
//                'packing_warehouse',
//                'Can fulfill from warehouse 99'
//            );
//            return;
//        }
//
////        if (OrderService::canNotFulfill($order)) {
////            $this->updateStatusWithLog(
////                $order,
////                'auto_missing_item',
////                'Order is missing one or more items'
////            );
////            return;
////        }
//
//        if (OrderService::canNotFulfill($order, 100)) {
//            $this->updateStatusWithLog(
//                $order,
//                'picking',
//                'We cannot fulfill from web only, has warehouse items, add to picking right away'
//            );
//            return;
//        }
    }

    /**
     * @param Order $order
     * @param string $newStatusCode
     * @param string $msg
     */
    private function updateStatusWithLog(Order $order, string $newStatusCode, string $msg): void
    {
        $order->update(['status_code' => $newStatusCode]);

        // Log event
        info(
            'PackingStatusesRules: set status to:',
            [
                'order_number' => $order->order_number,
                'nwe_status_code' => $newStatusCode,
                'msg' => $msg,
            ]
        );
    }
}
