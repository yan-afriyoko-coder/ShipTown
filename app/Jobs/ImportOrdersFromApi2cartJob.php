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
use Illuminate\Support\Collection;

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
        info('Job ImportOrdersFromApi2cartJob dispatched');
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
            $ordersCollection = $this->fetchRecentlyChangedOrders();

            $batches = array_chunk($ordersCollection, 20);

            foreach ($batches as $batch) {
                $this->convertAndSave($batch);

                $this->updateLastSyncedTimestamp($batch);
            }

        // keep going until we import all
        } while (count($ordersCollection) > 0);

        // finalize
        $this->finishedSuccessfully = true;
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

        foreach ($ordersCollection as $order)
        {
            $order['order_number']    = $order['id'];
            $order['order_placed_at'] = Carbon::createFromFormat($order['create_at']['format'],
                $order['create_at']['value']);
            $order['products_count']  = 0;
            $order['status_code']     = $order['status']['id'];

            $statuses = $this->getChronologicalStatusHistory($order);

            foreach ($statuses as $status) {
                if($status['id'] !== 'processing') {
                    $time = $status['modified_time'];
                    $order['order_closed_at'] = Carbon::createFromFormat($time['format'], $time['value']);
                    break;
                }
            }

            foreach ($order['order_products'] as $product) {
                $order['products_count'] += $product['quantity'];
            }

            $convertedOrdersCollection[] = $order;
        }

        return $convertedOrdersCollection;
    }

    /**
     * @param array $ordersCollection
     */
    private function updateLastSyncedTimestamp(array $ordersCollection)
    {
        $lastOrder = Arr::last($ordersCollection);

        ConfigurationApi2cart::query()->updateOrCreate([],[
            'last_synced_modified_at' => Carbon::createFromFormat(
                $lastOrder['modified_at']['format'],
                $lastOrder['modified_at']['value']
            )->addSecond()
        ]);
    }

    /**
     * @return array
     * @throws \App\Exceptions\Api2CartKeyNotSetException
     */
    private function fetchRecentlyChangedOrders(): array
    {
        $lastSyncedTimeStamp = $this->getLastSyncedTimestamp();

        return $this->fetchOrders($lastSyncedTimeStamp, 100);
    }

    /**
     * @param $batch
     */
    private function convertAndSave($batch): void
    {
        $transformedOrdersCollection = $this->convertOrdersFormat($batch);

        SaveOrdersCollection::dispatch($transformedOrdersCollection);
    }

    /**
     * @param array $order
     * @return Collection
     */
    public function getChronologicalStatusHistory(array $order){
        return Collection::make($order['status']['history'])
            ->sort(function ($a, $b) {
                $a_time = Carbon::make($a['modified_time']['value']);
                $b_time = Carbon::make($b['modified_time']['value']);
                return $a_time > $b_time;
            });
    }
}
