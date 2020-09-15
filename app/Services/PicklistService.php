<?php


namespace App\Services;

use App\Models\OrderProduct;
use App\Models\Pick;
use App\Models\Picklist;
use App\Models\PickRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
}
