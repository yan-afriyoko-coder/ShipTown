<?php


namespace App\Services;


use App\Events\OrderCreatedEvent;
use App\Events\OrderStatusChangedEvent;
use App\Models\Order;
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

    public static function updateOrCreate(array $data)
    {
        $order = Order::query()
            ->updateOrCreate(
                ["order_number" => $data['order_number']],
                $data
            );

        $order->orderProducts()->delete();

        foreach ($data['order_products'] as $rawOrderProduct) {

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

            $product = Product::query()
                ->where(['sku' => $rawOrderProduct['sku_ordered']])
                ->orWhere([
                    'sku' => Str::substr($rawOrderProduct['sku_ordered'], 0, 6)
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
