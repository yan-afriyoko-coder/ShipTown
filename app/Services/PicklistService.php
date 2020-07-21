<?php


namespace App\Services;


use App\Models\OrderProduct;
use App\Models\Picklist;
use Illuminate\Support\Arr;

/**
 * Class PicklistService
 * @package App\Services
 */
class PicklistService
{
    /**
     * @param OrderProduct|array $orderProduct
     * @return Picklist
     */
    public static function fromOrderProduct($orderProduct)
    {
        foreach (Arr::wrap($orderProduct) as $orderProduct) {
            Picklist::updateOrCreate([
                'order_product_id' => $orderProduct['id']
            ],[
                'order_id' => $orderProduct['order_id'],
                'product_id' => $orderProduct['product_id'],
                'location_id' => 'WWW',
                'sku_ordered' => $orderProduct['sku_ordered'],
                'name_ordered' => $orderProduct['name_ordered'],
                'quantity_requested' => $orderProduct['quantity'],
            ]);
        }
    }
}
