<?php


namespace App\Modules\StockControl\src\Listeners\OrderProductShipmentCreatedEvent;

use App\Events\OrderProductShipmentCreatedEvent;
use Exception;

class IncreaseDecreaseInventoryQuantityListener
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

        $inventory = $orderProductShipment->product
            ->inventory()->whereWarehouseId($orderProductShipment->warehouse_id)
            ->first();

        $inventory->quantity = $inventory->quantity - $orderProductShipment->quantity_shipped;
        $inventory->save();

        return true;
    }
}
