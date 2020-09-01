<?php


namespace App\Services;

use App\Events\Order\CreatedEvent;
use App\Events\Order\StatusChangedEvent;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class OrderService
{
    public static function addToPacklist(Order $order)
    {
        foreach ($order->orderProducts()->get() as $orderProduct) {
            PacklistService::addOrderProductPick($orderProduct);
        }
    }

    public static function addToPicklist(Order $order)
    {
        foreach ($order->orderProducts()->get() as $orderProduct) {
            PicklistService::addOrderProductToOldPicklist($orderProduct);
        }
    }

    /**
     * @param array $attributes
     * @return Order
     */
    public static function updateOrCreate(array $attributes)
    {
        $order = Order::updateOrCreate(
            ["order_number" => $attributes['order_number']],
            Arr::only($attributes, ['raw_import'])
        );

        self::updateOrCreateShippingAddress($order, $attributes['shipping_address']);

        $order->orderProducts()->delete();

        foreach ($attributes['order_products'] as $rawOrderProduct) {
            $orderProduct = OrderProduct::onlyTrashed()
                ->where([
                    'order_id' => $order->id,
                    'sku_ordered' => $rawOrderProduct['sku_ordered'],
                    'quantity_ordered' => $rawOrderProduct['quantity_ordered'],
                    'price' => $rawOrderProduct['price'],
                ])
                ->first();

            if ($orderProduct) {
                $orderProduct->restore();
                continue;
            }

            $orderProduct = new OrderProduct();
            $orderProduct->fill($rawOrderProduct);

            $extracted_sku = Str::substr($rawOrderProduct['sku_ordered'], 0, 6);

            $product = Product::query()
                ->whereIn('sku', [
                    $rawOrderProduct['sku_ordered'],
                    $extracted_sku
                ])
                ->first();

            $orderProduct->product_id = $product ? $product->getKey() : null;

            $order->orderProducts()->save($orderProduct);
        }

        CreatedEvent::dispatch($order);

        if (Arr::has($attributes, 'status_code')) {
            $order->update(['status_code' => $attributes['status_code']]);
        }

        return $order;
    }

    /**
     * @param array $shippingAddressAttributes
     * @param $order
     * @return Order
     */
    public static function updateOrCreateShippingAddress(Order $order, array $shippingAddressAttributes): Order
    {
        $shipping_address = OrderAddress::query()->findOrNew($order->shipping_address_id ?: 0);
        $shipping_address->fill($shippingAddressAttributes);
        $shipping_address->save();
        $order->shippingAddress()->associate($shipping_address);

        $order->save();

        return $order;
    }
}
