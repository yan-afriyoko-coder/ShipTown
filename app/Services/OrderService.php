<?php


namespace App\Services;

use App\Events\Order\CreatedEvent;
use App\Events\Order\StatusChangedEvent;
use App\Jobs\Api2cart\ImportShippingAddressJob;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\PickRequest;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderService
{
    public static function getOrderPdf($order_number, $template)
    {
        $order = Order::query()
            ->where(['order_number' => $order_number])
            ->with('shippingAddress')
            ->firstOrFail();

        if (!$order->shipping_address_id) {
            ImportShippingAddressJob::dispatchNow($order->id);
            $order = $order->refresh();
        }

        $view = 'pdf/orders/'. $template;
        $data = $order->toArray();

        return PdfService::fromView($view, $data);
    }

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::partial('q', 'order_number'),

                AllowedFilter::exact('status', 'status_code'),
                AllowedFilter::exact('order_number'),
                AllowedFilter::exact('packer_user_id'),

                AllowedFilter::scope('is_picked'),
                AllowedFilter::scope('is_packed'),
                AllowedFilter::scope('is_packing'),

                AllowedFilter::scope('has_packer'),
            ])
            ->allowedIncludes([
                'shipping_address',
                'order_shipments',
                'order_products',
                'order_products.product',
                'order_products.product.aliases',
                'packer',
            ])
            ->allowedSorts([
                'updated_at',
                'product_line_count',
                'total_quantity_ordered',
                'order_placed_at',
            ]);
    }

    /**
     * @param Order $order
     */
    public static function createPickRequests(Order $order): void
    {
        $orderProducts = $order->orderProducts()->get();

        foreach ($orderProducts as $orderProduct) {
            PickRequest::updateOrCreate([
                'order_product_id' => $orderProduct->getKey(),
            ], [
                'order_id' => $order->id,
                'quantity_required' => $orderProduct->quantity_ordered,
            ]);
        }

        debug('Pick requests created', [
            'order_number' => $order->order_number
        ]);
    }

    /**
     * @param array $attributes
     * @return Order
     */
    public static function updateOrCreate(array $attributes)
    {
        $order = Order::updateOrCreate(
            ["order_number" => $attributes['order_number']],
            Arr::only($attributes, ['status_code', 'raw_import'])
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
