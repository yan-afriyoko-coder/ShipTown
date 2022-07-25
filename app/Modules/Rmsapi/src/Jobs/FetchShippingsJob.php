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

class FetchShippingsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var RmsapiConnection */
    private $rmsapiConnection;

    private string $batch_uuid;

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



        collect($response->getResult())->each(function ($shippingRecord) {
            Log::debug('Importing record', ["rmsapi_shipping_record" => $shippingRecord]);

            $uuid = $this->rmsapiConnection->location_id . '-shipping.id-' . $shippingRecord['ID'];

            if (OrderProduct::query()->where(['custom_unique_reference_id' => $uuid])->exists()) {
                return;
            }

            $order = $this->firstOrCreateOrder($shippingRecord);

            $product = Product::findBySKU($shippingRecord['ItemLookupCode']);

            /** @var OrderProduct $orderProduct */
            $orderProduct = OrderProduct::create([
                'custom_unique_reference_id' => $uuid,
                'order_id' => $order->getKey(),
                'product_id' => $product ? $product->getKey() : null,
                'sku_ordered' => $shippingRecord['ItemLookupCode'],
                'name_ordered' => $shippingRecord['Description'],
                'quantity_ordered' => $shippingRecord['Quantity'],
                'price' => $shippingRecord['Price'],
            ]);

//             enable when ready !!!
            if ($orderProduct->product_id) {
                $inventory = Inventory::query()->where([
                    'product_id' => $orderProduct->product_id,
                    'warehouse_code' => $this->rmsapiConnection->location_id,
                ])->get();

                $inventory->each(function (Inventory $inventoryRecord) use ($shippingRecord) {
                    InventoryService::adjustQuantity(
                        $inventoryRecord,
                        $shippingRecord['Quantity'],
                        'rmsapi_shipping_import'
                    );

                    $inventoryRecord->product->log('Imported RMS shipping, restocking', [
                        'warehouse_code' => $inventoryRecord->warehouse_code,
                        'quantity' => $shippingRecord['Quantity'],
                    ]);
                });
            }

            $order->update([
                'total_shipping' => $shippingRecord['Charge'],
                'total' => $order->orderProductsTotals->total_price + $shippingRecord['Charge'],
                'total_paid' => $order->orderProductsTotals->total_price + $shippingRecord['Charge'],
            ]);

            $this->rmsapiConnection->update(['shippings_last_timestamp' => $shippingRecord['DBTimeStamp']]);
        });

        return true;
    }

    /**
     * @param array $record
     * @return Order
     */
    private function firstOrCreateOrder(array $record): Order
    {
        $order = Order::firstOrCreate([
            'order_number' => $this->rmsapiConnection->location_id. '-TRN-' . $record['TransactionNumber'],
            'status_code' => 'imported_rms_shippings'
        ], []);

        if (! $order->wasRecentlyCreated) {
            return $order;
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
            'country_code' => $record['Country'],
            'country_name' => $record['Country'],
            'phone' => $record['PhoneNumber'],
            'fax' => '',
            'website' => '',
            'region' => '',
        ]);

        $order->shipping_address_id = $shippingAddress->getKey();

        if (! empty($record['TransactionEntryComment'])) {
            OrderComment::create([
                'order_id' => $order->getKey(),
                'comment' => $record['TransactionEntryComment'],
            ]);
        }

        return $order;
    }
}
