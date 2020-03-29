<?php

namespace App\Jobs;

use App\Managers\CompanyConfigurationManager;
use App\Models\ConfigurationApi2cart;
use App\Models\Order;
use App\Modules\Api2cart\src\Orders;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

class ImportOrdersFromApi2cartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var bool
     */
    public $finishedSuccessfully;

    /**
     * Create a new job instance.
     *
     */
    public function __construct()
    {
        $this->finishedSuccessfully = false;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $lastSyncedTimeStamp = $this->getLastSyncedTimestamp();

        $ordersCollection = $this->fetchOrders($lastSyncedTimeStamp, 10);

        $transformedOrdersCollection = $this->convertOrdersFormat($ordersCollection);

        $this->satLastSyncedTimestamp($ordersCollection);

        $this->pushCollection($transformedOrdersCollection);

        // save orders
        foreach ($transformedOrdersCollection as $order) {
            Order::query()->updateOrCreate(
                [
                    "order_number" => $order['order_number'],
                ],
                array_merge(
                    $order,
                    ['order_as_json' => $order]
                )
            );

            ConfigurationApi2cart::query()->updateOrCreate([],[
                'last_synced_modified_at' => Carbon::createFromFormat(
                    $order['original_json']['modified_at']['format'],
                    $order['original_json']['modified_at']['value']
                )
            ]);

        }

        // finalize
        $this->finishedSuccessfully = true;
    }

    /**
     * @param array $products
     * @return array
     */
    public function convertProducts(array $products) {

        $result = [];

        foreach ($products as $product) {
            $result[] = [
                'sku' => $product['model'],
                'price' => $product['price'],
                'quantity' => $product['quantity']
            ];
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getLastSyncedTimestamp() {

        $config = ConfigurationApi2cart::query()->firstOrCreate([],[]);

        return $config['last_synced_modified_at'];

    }

    /**
     * @param mixed $timestamp
     * @param int $count
     * @return array
     * @throws \App\Exceptions\Api2CartKeyNotSetException
     */
    private function fetchOrders($timestamp, int $count){

        // initialize variables
        $params = [
            'params' => 'force_all',
            'sort_by' => 'modified_at',
            'sort_direction' => 'asc',
            'count' => $count,
            'modified_from' => $timestamp,
        ];

        $api2cart_store_key = CompanyConfigurationManager::getBridgeApiKey();

        return Orders::getOrdersCollection($api2cart_store_key, $params);
    }

    /**
     * @param array $ordersCollection
     * @return array
     */
    private function convertOrdersFormat(array $ordersCollection)
    {
        $convertedOrdersCollection = [];

        foreach ($ordersCollection['order'] as $order) {

            $convertedOrdersCollection[] = [
                'order_number' => $order['order_id'],
                'original_json' => $order,
                'products' => Arr::has($order, 'order_products')
                    ? $this->convertProducts($order['order_products'])
                    : [],
            ];

        }

        return $convertedOrdersCollection;
    }

    /**
     * @param array $ordersCollection
     */
    private function satLastSyncedTimestamp(array $ordersCollection){

    }

    /**
     * @param array $transformedOrdersCollection
     */
    private function pushCollection(array $transformedOrdersCollection){

    }
}
