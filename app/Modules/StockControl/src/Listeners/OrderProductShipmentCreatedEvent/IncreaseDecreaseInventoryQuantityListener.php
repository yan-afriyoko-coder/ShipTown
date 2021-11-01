<?php


namespace App\Modules\StockControl\src\Listeners\OrderProductShipmentCreatedEvent;

use App\Events\OrderProductShipmentCreatedEvent;
use App\Models\Inventory;
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

        $inventory = Inventory::firstOrCreate([
                'product_id' => $orderProductShipment->product_id,
                'warehouse_id' => $orderProductShipment->warehouse_id,
            ], [
                'location_id' => $orderProductShipment->warehouse->code
            ]);

        $inventory->quantity = $inventory->quantity - $orderProductShipment->quantity_shipped;
        $inventory->save();

        return true;
    }
}
