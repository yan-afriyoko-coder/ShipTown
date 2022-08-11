<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderComment;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Services\InventoryService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ImportShippingsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var RmsapiConnection */
    private $rmsapiConnection;

    private string $batch_uuid;
    private OrderProduct $orderProduct;

    /**
     * Create a new job instance.
     *
     * @param int $rmsapiConnectionId
     *
     * @throws Exception
     */
    public function __construct(int $rmsapiConnectionId)
    {
        $this->rmsapiConnection = RmsapiConnection::find($rmsapiConnectionId);
        $this->batch_uuid = Uuid::uuid4()->toString();
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle(): bool
    {
        $params = [
            'per_page'            => 100,
            'order_by'            => 'DBTimeStamp:asc',
            'ShippingCarrierName' => 'PM',
            'min:DBTimeStamp' => $this->rmsapiConnection->shippings_last_timestamp,
        ];

        try {
            $response = RmsapiClient::GET($this->rmsapiConnection, 'api/shippings', $params);
        } catch (GuzzleException $e) {
            Log::warning('Failed RMSAPI product fetch', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return false;
        }

        collect($response->getResult())
            ->each(function ($shippingRecord) {
                $orderProduct = $this->createOrderProductFrom($shippingRecord);

                if ($orderProduct) {
                    $this->restockOriginForStockToBalance($orderProduct);
                }
            });

        return true;
    }

    /**
     * @param array $record
     * @return Order
     */
    private function firstOrCreateOrder(array $record): Order
    {
        /** @var Order $order */
        $order = Order::firstOrCreate([
            'order_number' => $this->rmsapiConnection->location_id. '-TRN-' . $record['TransactionNumber'],
        ], [
            'status_code' => 'imported_rms_shippings',
            'order_placed_at' => $record['ShippingDateCreated'],
            'shipping_method_code' => $record['ShippingServiceName'],
            'shipping_method_name' => $record['ShippingCarrierName'],
        ]);

        if (! $order->wasRecentlyCreated) {
            return $order;
        }

        if (! empty($record['TransactionComment'])) {
            OrderComment::create([
                'order_id' => $order->getKey(),
                'comment' => trim($record['TransactionComment']),
            ]);
        }

        /** @var OrderAddress $shippingAddress */
        $shippingAddress = OrderAddress::create([
            'company' => $record['Company'],
            'first_name' => $record['Name'],
            'last_name' => $record['Name'],
            'email' => $record['EmailAddress'],
            'address1' => $record['Address'],
            'address2' => $record['Address2'],
            'postcode' => $record['Zip'],
            'city' => $record['City'],
            'state_code' => $record['State'],
            'state_name' => $record['State'],
            'country_code' => empty(trim(' ')) ? 'IRL' : $record['Country'],
            'country_name' => empty(trim(' ')) ? 'Ireland' : $record['Country'],
            'phone' => $record['PhoneNumber'],
            'fax' => '',
            'website' => '',
            'region' => '',
        ]);

        $order->update(['shipping_address_id' => $shippingAddress->getKey()]);

        $order->logActivity('Order imported from RMS API', [
            'warehouse_code' => $this->rmsapiConnection->location_id,
            'transaction_number' => $record['TransactionNumber'],
        ]);

        return $order;
    }

    /**
     * @param $shippingRecord
     * @return OrderProduct|null:
     */
    private function createOrderProductFrom($shippingRecord): ?OrderProduct
    {
        Log::debug('Importing record', ["rmsapi_shipping_record" => $shippingRecord]);

        $uuid = $this->rmsapiConnection->location_id . '-shipping.id-' . $shippingRecord['ID'];

        if (OrderProduct::query()->where(['custom_unique_reference_id' => $uuid])->exists()) {
            Log::debug('Record already exists', ["rmsapi_shipping_record" => $shippingRecord]);
            return null;
        }

        $order = $this->firstOrCreateOrder($shippingRecord);

        $product = Product::findBySKU($shippingRecord['ItemLookupCode']);

        $orderProduct = OrderProduct::create([
            'custom_unique_reference_id' => $uuid,
            'order_id' => $order->getKey(),
            'product_id' => $product ? $product->getKey() : null,
            'sku_ordered' => $shippingRecord['ItemLookupCode'],
            'name_ordered' => $shippingRecord['ItemDescription'],
            'quantity_ordered' => $shippingRecord['TransactionEntryQuantity'],
            'price' => $shippingRecord['TransactionEntryPrice'],
        ]);

        if (! empty($record['TransactionEntryComment'])) {
            OrderComment::create([
                'order_id' => $order->getKey(),
                'comment' => trim($record['TransactionEntryComment']),
            ]);
        }

        $order->update([
            'total_shipping' => $shippingRecord['ShippingCharge'],
            'total' => $order->orderProductsTotals->total_price + $shippingRecord['ShippingCharge'],
            'total_paid' => $order->orderProductsTotals->total_price + $shippingRecord['ShippingCharge'],
        ]);

        $this->rmsapiConnection->update(['shippings_last_timestamp' => $shippingRecord['DBTimeStamp']]);

        return $orderProduct;
    }

    /**
     * When creating shipping in RMS, it automatically creates transaction and deducts stock
     * We not 100% sure where the stock is gonna be shipped from so
     * We will restock sold products and reserve from all stock until shipped
     * then transaction will be created in warehouse where product ships from
     *
     * @param OrderProduct $orderProduct
     */
    private function restockOriginForStockToBalance(OrderProduct $orderProduct): void
    {
        if ($orderProduct->product_id === null) {
            return;
        }

        Inventory::query()
            ->where([
                'product_id'     => $orderProduct->product_id,
                'warehouse_code' => $this->rmsapiConnection->location_id,
            ])
            ->get()
            ->each(function (Inventory $inventoryRecord) use ($orderProduct) {
                InventoryService::adjustQuantity(
                    $inventoryRecord,
                    $orderProduct->quantity_ordered,
                    'rmsapi_shipping_import'
                );

                $inventoryRecord->product->log('Imported RMS shipping, restocking', [
                    'warehouse_code' => $inventoryRecord->warehouse_code,
                    'quantity' => $orderProduct->quantity_ordered,
                ]);
            });
    }
}
