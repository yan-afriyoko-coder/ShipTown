<?php


namespace App\Services;

use App\Events\Order\CreatedEvent;
use App\Events\Order\StatusChangedEvent;
use App\Jobs\Api2cart\ImportShippingAddressJob;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\PickRequest;
use App\Models\Product;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use phpseclib\Math\BigInteger;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderService
{
    /**
     * @param Order $order
     * @param $sourceLocationId
     * @return bool
     */
    public static function canNotFulfill(Order $order, $sourceLocationId = null)
    {
        return !self::canFulfill($order, $sourceLocationId);
    }

    /**
     * @param Order $order
     * @param $sourceLocationId
     * @return bool
     */
    public static function canFulfill(Order $order, $sourceLocationId = null)
    {
        $products = $order->orderProducts()->get();

        foreach ($products as $product) {
            $query = Inventory::where('product_id', $product->product_id);

            if ($sourceLocationId) {
                $query->where('location_id', $sourceLocationId);
            }

            $quantity_available = $query->sum(\DB::raw('(quantity - quantity_reserved)'));

            if (!$quantity_available) {
                return false;
            }

            if ((double) $quantity_available < (double) $product->quantity_ordered) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $order_number
     * @param $template_name
     * @return string
     */
    public static function getOrderPdf(string $order_number, $template_name)
    {
        $order = Order::query()
            ->where(['order_number' => $order_number])
            ->with('shippingAddress')
            ->firstOrFail();

        if (!$order->shipping_address_id) {
            ImportShippingAddressJob::dispatchNow($order->id);
            $order = $order->refresh();
        }

        $view = 'pdf/orders/'. $template_name;
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
                AllowedFilter::partial('search', 'order_number'),

                AllowedFilter::exact('status', 'status_code'),
                AllowedFilter::exact('order_number')->ignore([null, '']),
                AllowedFilter::exact('packer_user_id'),

                AllowedFilter::scope('is_picked'),
                AllowedFilter::scope('is_packed'),
                AllowedFilter::scope('is_packing'),

                AllowedFilter::scope('has_packer'),

                AllowedFilter::scope('inventory_source_location_id', 'addInventorySource')->default(100),
            ])
            ->allowedIncludes([
                'stats',
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
                'order_closed_at',
                'min_shelf_location',
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

        logger('Pick requests created', [
            'order_number' => $order->order_number
        ]);
    }

    /**
     * @param array $orderAttributes
     * @return Order
     */
    public static function updateOrCreate(array $orderAttributes)
    {
        $order = Order::updateOrCreate(
            ['order_number' => $orderAttributes['order_number']],
            $orderAttributes
        );

        self::updateOrCreateShippingAddress($order, $orderAttributes['shipping_address']);

        $order = self::syncOrderProducts($orderAttributes['order_products'], $order);

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

    /**
     * @param array $orderProductAttributes
     * @return BigInteger|null
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
     * @return Order
     * @throws \Exception
     */
    private static function syncOrderProducts($order_products, Order $order): Order
    {
        $orderProductIdsToKeep = [];

        foreach ($order_products as $orderProductAttributes) {
            $orderProduct = OrderProduct::where(['order_id' => $order->getKey()])
                ->whereNotIn('id', $orderProductIdsToKeep)
                ->updateOrCreate(
                // attributes
                    collect($orderProductAttributes)
                        ->only([
                            'sku_ordered',
                            'name_ordered',
                            'quantity_ordered',
                            'price',
                        ])
                        ->toArray(),
                    // values
                    [
                        'order_id' => $order->getKey(),
                        'product_id' => self::getProductId($orderProductAttributes),
                    ]
                );

            $orderProductIdsToKeep[] = $orderProduct->getKey();
        }

        OrderProduct::where(['order_id' => $order->id])
            ->whereNotIn('id', $orderProductIdsToKeep)
            ->delete();

        return $order->refresh();
    }
}
