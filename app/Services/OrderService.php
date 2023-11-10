<?php

namespace App\Services;

use App\Events\Order\OrderCreatedEvent;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Modules\Api2cart\src\Jobs\ImportShippingAddressJob;
use App\Modules\InventoryReservations\src\Models\Configuration;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * @param Order $order
     * @param $sourceLocationId
     *
     * @return bool
     */
    public static function canNotFulfill(Order $order, $sourceLocationId = null): bool
    {
        return !self::canFulfill($order, $sourceLocationId);
    }

    /**
     * @param Order $order
     * @param $sourceLocationId
     *
     * @return bool
     */
    public static function canFulfill(Order $order, $sourceLocationId = null): bool
    {
        $orderProducts = $order->orderProducts()->get();

        foreach ($orderProducts as $orderProduct) {
            if (self::canNotFulfillOrderProduct($orderProduct, $sourceLocationId)) {
                return false;
            }
        }

        return true;
    }

    public static function getOrderPdf(string $order_number, string $template_name): string
    {
        /** @var Order $order */
        $order = Order::query()
            ->where(['order_number' => $order_number])
            ->with('shippingAddress')
            ->firstOrFail();

        if (!$order->shipping_address_id) {
            ImportShippingAddressJob::dispatchSync($order->id);
            $order = $order->refresh();
        }

        $view = 'pdf/orders/'.$template_name;
        $data = $order->toArray();

        return PdfService::fromView($view, $data);
    }

    /**
     * @param array $orderAttributes
     *
     * @throws Exception
     *
     * @return Order
     */
    public static function updateOrCreate(array $orderAttributes): Order
    {
        $order = Order::whereOrderNumber($orderAttributes['order_number'])->firstOrNew();
        $order->fill($orderAttributes);
        $order->is_editing = true;
        $order->save();

        self::updateOrCreateShippingAddress($order, $orderAttributes['shipping_address']);

        $order = self::syncOrderProducts($orderAttributes['order_products'], $order);

        OrderCreatedEvent::dispatch($order);

        $order->is_editing = false;
        $order->save();

        return $order;
    }

    public static function updateOrCreateShippingAddress(Order $order, array $shippingAddressAttributes): Order
    {
        $shipping_address = OrderAddress::query()->findOrNew($order->shipping_address_id ?: 0);
        $shipping_address->fill($shippingAddressAttributes);
        $shipping_address->save();

        $order->shippingAddress()->associate($shipping_address);
        $order->save();

        return $order;
    }

    /**
     * @param array $orderProductAttributes
     *
     * @return null
     */
    private static function getProductId(array $orderProductAttributes)
    {
        $product = ProductService::find($orderProductAttributes['sku_ordered']);
        if ($product) {
            return  $product->id;
        }

        $extractedSku = Str::substr($orderProductAttributes['sku_ordered'], 0, 6);
        $product = ProductService::find($extractedSku);
        if ($product) {
            return  $product->id;
        }

        return null;
    }

    /**
     * @param $order_products
     * @param Order $order
     *
     * @throws Exception
     *
     * @return Order
     */
    private static function syncOrderProducts($order_products, Order $order): Order
    {
        $orderProductIdsToKeep = [];

        foreach ($order_products as $orderProductAttributes) {
            $orderProduct = OrderProduct::query()->where(['order_id' => $order->getKey()])
                ->whereNotIn('id', $orderProductIdsToKeep)
                ->updateOrCreate(
                    // attributes
                    collect($orderProductAttributes)
                        ->only([
                            'sku_ordered',
                            'name_ordered',
                            'quantity_ordered',
                        ])
                        ->toArray(),
                    // values
                    [
                        'order_id'   => $order->getKey(),
                        'product_id' => self::getProductId($orderProductAttributes),
                        'price' => $orderProductAttributes['price'],
                    ]
                );

            $orderProductIdsToKeep[] = $orderProduct->getKey();
        }

        OrderProduct::query()
            ->where(['order_id' => $order->id])
            ->whereNotIn('id', $orderProductIdsToKeep)
            ->delete();

        return $order->refresh();
    }

    /**
     * @param OrderProduct $orderProduct
     * @param null $sourceLocationId
     * @return bool
     */
    public static function canFulfillOrderProduct(OrderProduct $orderProduct, $sourceLocationId = null): bool
    {
        if ($orderProduct->product_id) {
            return self::canFulfillProduct(
                $orderProduct->product_id,
                $orderProduct->quantity_to_ship,
                $sourceLocationId
            );
        }

        return false;
    }

    /**
     * @param OrderProduct $orderProduct
     * @param null $sourceLocationId
     * @return bool
     */
    public static function canNotFulfillOrderProduct(OrderProduct $orderProduct, $sourceLocationId = null): bool
    {
        return !self::canFulfillOrderProduct($orderProduct, $sourceLocationId);
    }

    /**
     * @param int $product_id
     * @param float $quantity
     * @param $sourceLocationId
     * @return bool
     */
    public static function canFulfillProduct(int $product_id, float $quantity, $sourceLocationId): bool
    {
        if ($quantity <= 0) {
            return true;
        }

        /** @var Configuration $baseModel */
        $baseModel = Configuration::first();

        $inventoryReservationsWarehouseId = $baseModel->warehouse_id;
        $query = Inventory::query()
            ->where('product_id', $product_id)
            ->where('warehouse_id', '!=', $inventoryReservationsWarehouseId);

        if ($sourceLocationId) {
            $query->where('warehouse_code', $sourceLocationId);
        }

        $quantity_available = $query->sum(DB::raw('(quantity - quantity_reserved)'));

        if (!$quantity_available) {
            return false;
        }

        if ((float)$quantity_available < $quantity) {
            return false;
        }

        return true;
    }
}
