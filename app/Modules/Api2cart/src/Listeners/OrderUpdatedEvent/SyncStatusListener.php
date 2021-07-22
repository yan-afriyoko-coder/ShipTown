<?php


namespace App\Modules\Api2cart\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\Api2cart\src\Api\Orders;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;

class SyncStatusListener
{
    public function handle(OrderUpdatedEvent $event)
    {
        if ($event->order->isAttributeChanged('status_code')) {
            $orderImport = Api2cartOrderImports::where(['order_number' => $event->order->order_number])
                ->latest()
                ->first();

            $api2cartConnection = Api2cartConnection::find($orderImport->connection_id);

            Orders::update($api2cartConnection->bridge_api_key, [
                'order_id' => $orderImport->api2cart_order_id,
                'order_status' => $event->order->status_code
            ]);
        }
    }
}
