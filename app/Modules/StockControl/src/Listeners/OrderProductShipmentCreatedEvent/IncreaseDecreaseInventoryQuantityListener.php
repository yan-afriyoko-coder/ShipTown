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

        if ($orderProductShipment->warehouse_id === null) {
            return true;
        }

        /** @var Inventory $inventory */
        $inventory = Inventory::firstOrCreate([
                'product_id' => $orderProductShipment->product_id,
                'warehouse_id' => $orderProductShipment->warehouse_id,
            ]);

        $inventory->quantity = $inventory->quantity - $orderProductShipment->quantity_shipped;
        $inventory->save();

        return true;
    }
}
