<?php


namespace App\Services;

use App\Models\OrderProduct;
use App\Models\Pick;
use App\Models\Picklist;
use App\Models\PickRequest;
use Illuminate\Support\Arr;

/**
 * Class PicklistService
 * @package App\Services
 */
class PicklistService
{
    /**
     * @param PickRequest $pickRequest
     */
    public static function addToPicklist(PickRequest $pickRequest)
    {
        $orderProduct = $pickRequest->orderProduct()->first();

        $pick = Pick::firstOrCreate([
            'product_id' => $orderProduct->product_id,
            'sku_ordered' => $orderProduct->sku_ordered,
            'name_ordered' => $orderProduct->name_ordered,
            'picked_at' => null,
        ], [
            'quantity_required' => 0
        ]);

        $pick->increment('quantity_required', $orderProduct->quantity_ordered);

        $pickRequest->update(['pick_id' => $pick->getKey()]);
    }

    /**
     * @param PickRequest $pickRequest
     */
    public static function removeFromPicklist(PickRequest $pickRequest)
    {
        $orderProduct = $pickRequest->orderProduct()->first();

        $pick = $pickRequest->pick();

        $pick->decrement('quantity_required', $orderProduct->quantity_ordered);

        $pickRequest->update(['pick_id' => null]);
    }

    /**
     * @param OrderProduct|array $orderProduct
     * @return void
     */
    public static function addOrderProductPick($orderProduct)
    {
        foreach (Arr::wrap($orderProduct) as $orderProduct) {
            Picklist::updateOrCreate([
                'order_product_id' => $orderProduct['id']
            ], [
                'order_id' => $orderProduct['order_id'],
                'product_id' => $orderProduct['product_id'],
                'sku_ordered' => $orderProduct['sku_ordered'],
                'name_ordered' => $orderProduct['name_ordered'],
                'quantity_requested' => $orderProduct['quantity_ordered'],
            ]);
        }
    }

    /**
     *
     * @param OrderProduct|array $orderProduct
     * @return void
     */
    public static function removeOrderProductPick($orderProduct)
    {
        foreach (Arr::wrap($orderProduct) as $orderProduct) {
            Picklist::query()
                ->where('order_product_id', '=', $orderProduct['id'])
                ->whereNull('picked_at')
                ->delete();
        }
    }
}
