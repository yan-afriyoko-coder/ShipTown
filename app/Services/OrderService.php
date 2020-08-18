<?php


namespace App\Services;


use App\Events\OrderCreatedEvent;
use App\Events\OrderStatusChangedEvent;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\Product;
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
            PicklistService::addOrderProductPick($orderProduct);
        }
    }

    /**
     * @param array $attributes
     * @return Order
     */
    public static function updateOrCreate(array $attributes)
    {
        $order = Order::firstOrNew(
            ["order_number" => $attributes['order_number']]
            , $attributes
        );

        $shipping_address = OrderAddress::query()->findOrNew($order->shipping_id ?: 0);
        $shipping_address->fill($attributes['shipping_address']);
        $shipping_address->save();
        $order->shippingAddress()->associate($shipping_address);

        $order->save();

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

            if($orderProduct) {
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

        OrderCreatedEvent::dispatch($order);
        OrderStatusChangedEvent::dispatch($order);

        return $order;
    }
}
