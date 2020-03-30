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
        do {
            $lastSyncedTimeStamp = $this->getLastSyncedTimestamp();

            $ordersCollection = $this->fetchOrders($lastSyncedTimeStamp, 100, 'force_all');

            $batches = array_chunk($ordersCollection, 10);

            foreach ($batches as $batch) {
                $transformedOrdersCollection = $this->convertOrdersFormat($batch);

                SaveOrdersCollection::dispatchNow($transformedOrdersCollection);

                $this->satLastSyncedTimestamp($batch);
            }

        } while (count($ordersCollection) > 0);

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
     * @param string $params
     * @return array
     * @throws \App\Exceptions\Api2CartKeyNotSetException
     */
    private function fetchOrders($timestamp, int $count, string $params = 'force_all'){

        // initialize params
        $params = [
            'params' => $params,
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

        foreach ($ordersCollection as $order) {

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
    private function satLastSyncedTimestamp(array $ordersCollection)
    {
        $lastOrder = Arr::last($ordersCollection);

        ConfigurationApi2cart::query()->updateOrCreate([],[
            'last_synced_modified_at' => Carbon::createFromFormat(
                $lastOrder['modified_at']['format'],
                $lastOrder['modified_at']['value']
            )->addSecond()
        ]);
    }
}
