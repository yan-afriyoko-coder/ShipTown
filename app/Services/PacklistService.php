<?php


namespace App\Services;

use App\Models\OrderProduct;
use App\Models\Packlist;
use Illuminate\Support\Arr;

/**
 * Class PicklistService
 * @package App\Services
 */
class PacklistService
{
    /**
     * @param OrderProduct|array $orderProduct
     * @return void
     */
    public static function addOrderProductPick($orderProduct)
    {
        foreach (Arr::wrap($orderProduct) as $orderProduct) {
            Packlist::updateOrCreate([
                'order_product_id' => $orderProduct['id']
            ], [
                'order_id' => $orderProduct['order_id'],
                'product_id' => $orderProduct['product_id'],
                'location_id' => 'WWW',
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
            Packlist::query()
                ->where('order_product_id', '=', $orderProduct['id'])
                ->whereNull('picked_at')
                ->delete();
        }
    }
}
