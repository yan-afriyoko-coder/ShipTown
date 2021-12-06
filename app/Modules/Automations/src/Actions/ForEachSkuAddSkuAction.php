<?php

namespace App\Modules\Automations\src\Actions;

use App\Models\OrderProduct;
use App\Models\Product;
use App\Modules\Automations\src\BaseOrderAction;

class ForEachSkuAddSkuAction extends BaseOrderAction
{
    public function handle(string $options = '')
    {
        parent::handle($options);

        $skus = collect(explode(',', $options));

        if ($skus->count() < 2) {
            $this->order->log('Automation: Incorrect action value. Correct format: BundleSKU,SKU1,SKU2,SKU3...');
            return false;
        }

        $skuToFind = $skus->first();
        $skuToAdd = $skus->last();

        if ($skuToFind === '') {
            $this->order->log('Automation: Incorrect action value. Correct format: SKU,SKU');
            return false;
        }

        if ($skuToAdd === '') {
            $this->order->log('Automation: Incorrect action value. Correct format: SKU,SKU');
            return false;
        }

        if ($skuToFind === $skuToAdd) {
            $this->order->log('Automation: Incorrect action value. Same SKUs not allowed');
            return false;
        }

        $product = Product::skuOrAlias($skuToAdd)->first();
        if ($product === null) {
            $this->order->log('Automation: Incorrect action value. SKU not exists: "'.$skuToAdd.'"');
            return false;
        }

        $this->order->is_editing = true;
        $this->order->save();

        $orderProducts = $this->order->orderProducts()
            ->where(['sku_ordered' => $skuToFind])
            ->where('quantity_to_ship', '>', 0)
            ->get();

        $orderProducts->each(function (OrderProduct $orderProduct) use ($product, $skuToAdd) {
            $newOrderProduct = new OrderProduct([]);
            $newOrderProduct->sku_ordered       = $skuToAdd;
            $newOrderProduct->order_id          = $orderProduct->order_id;
            $newOrderProduct->quantity_ordered  = $orderProduct->quantity_to_ship;
            $newOrderProduct->product_id        = $product->id;
            $newOrderProduct->name_ordered      = $product->name;
            $newOrderProduct->price             = $product->price;


            $orderProduct->quantity_split = $orderProduct->quantity_to_ship;
            $orderProduct->save();

            $newOrderProduct->save();
        });

        $this->order->is_editing = false;
        $this->order->save();

        return true;
    }
}
