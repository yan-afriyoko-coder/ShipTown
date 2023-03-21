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


        InventoryService::sellProduct(
            $orderProductShipment->inventory,
            - $orderProductShipment->quantity_shipped,
            'shipped',
            implode('', [
                'order_product_shipment_id_',
                $orderProductShipment->getKey(),
                '_shipped'
            ])
        );

        return true;
    }
}
