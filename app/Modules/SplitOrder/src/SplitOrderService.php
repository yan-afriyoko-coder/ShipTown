<?php

namespace App\Modules\SplitOrder\src;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Warehouse;

/**
 *
 */
class SplitOrderService
{
    /**
     * @var Warehouse
     */
    private Warehouse $warehouse;

    /**
     * @var Order|null
     */
    private ?Order $newOrder = null;

    /**
     * @var string|null
     */
    private ?string $newOrderStatus;

    /**
     * @var Order
     */
    private Order $originalOrder;

    public function split(Order $order, Warehouse $warehouse, string $newOrderStatus)
    {
        $this->originalOrder = $order;
        $this->newOrderStatus = $newOrderStatus;
        $this->warehouse = $warehouse;

        self::extractFulfillableProducts();
    }

    /**
     * @return void
     */
    private function extractFulfillableProducts(): void
    {
        $this->originalOrder->orderProducts
            ->filter(function (OrderProduct $orderProductOriginal) {
                return $orderProductOriginal->quantity_to_ship > 0;
            })
            ->each(function (OrderProduct $original) use (&$orderProductsToExtract) {
                /** @var Inventory $inventory */
                $inventory = $original->product->inventory()
                    ->where(['warehouse_id' => $this->warehouse->getKey()])
                    ->first();

                $quantityToExtract = min($inventory->quantity_available, $original->quantity_to_ship);

                if ($quantityToExtract <= 0.00) {
                    // return true to continue Eloquent each loop
                    return true;
                }

                $new = $original->replicate([
                    'order_id',
                    'quantity_ordered',
                    'quantity_split',
                    'quantity_picked',
                    'quantity_skipped_picking',
                ]);
                $new->order()->associate($this->getOrderOrCreate());
                $new->save();

                $original->increment('quantity_split', $quantityToExtract);
                $new->increment('quantity_ordered', $quantityToExtract);
                $inventory->increment('quantity_reserved', $quantityToExtract);

                // return true to continue Eloquent each loop
                return true;
            });

        if ($this->newOrder) {
            $this->originalOrder->is_editing = false;
            $this->originalOrder->save();

            $this->newOrder->is_editing = false;
            $this->newOrder->save();
        }
    }

    /**
     * @return Order
     */
    private function getOrderOrCreate(): Order
    {
        if ($this->newOrder) {
            return $this->newOrder;
        }

        $this->originalOrder->is_editing = true;
        $this->originalOrder->save();

        $this->newOrder = $this->originalOrder->replicate();
        $this->newOrder->status_code = $this->newOrderStatus;
        $this->newOrder->is_editing = true;
        $this->newOrder->order_number .= '-PARTIAL-' . $this->warehouse->code;
        $this->newOrder->save();

        return $this->newOrder;
    }
}
