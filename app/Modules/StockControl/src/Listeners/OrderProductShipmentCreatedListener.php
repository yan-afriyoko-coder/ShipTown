<?php


namespace App\Modules\StockControl\src\Listeners;

use App\Events\OrderProductShipmentCreatedEvent;
use App\Services\InventoryService;
use Exception;

class OrderProductShipmentCreatedListener
{
    /**
     * @throws Exception
     */
    public function handle(OrderProductShipmentCreatedEvent $event): bool
    {
        $orderProductShipment = $event->orderProductShipment;

        if ($orderProductShipment->product_id === null) {
            return true;
        }

        if ($orderProductShipment->warehouse_id === null) {
            return true;
        }

        InventoryService::adjustQuantity(
            $orderProductShipment->inventory,
            - $orderProductShipment->quantity_shipped,
            'shipped'
        );

        return true;
    }
}
