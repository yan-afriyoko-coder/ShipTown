<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Heartbeat;
use App\Modules\Api2cart\src\Api\Orders;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ImportOrdersJobs extends UniqueJob
{
    public bool $finishedSuccessfully;

    private Api2cartConnection $api2cartConnection;

    public function __construct(Api2cartConnection $api2cartConnection)
    {
        $this->api2cartConnection = $api2cartConnection;
        $this->finishedSuccessfully = false;
    }

    public function uniqueId(): string
    {
        return implode('-', [parent::uniqueId(), $this->api2cartConnection->id]);
    }

    public function handle()
    {
        $this->importOrders($this->api2cartConnection);

        // finalize
        $this->finishedSuccessfully = true;
    }

    /**
     * @throws Exception|GuzzleException
     */
    private function importOrders(Api2cartConnection $api2cartConnection): void
    {
        $batchSize = 100;

        // initialize params
        $params = [
            'params' => 'force_all',
            'created_from' => '2022-01-01 00:00:00',
            'sort_by' => 'modified_at',
            'sort_direction' => 'asc',
            'count' => $batchSize,
        ];

        if ($api2cartConnection->magento_store_id) {
            $params['store_id'] = $api2cartConnection->magento_store_id;
        }

        if (isset($api2cartConnection->last_synced_modified_at)) {
            $params = Arr::add(
                $params,
                'modified_from',
                $api2cartConnection->last_synced_modified_at
            );
        }

        $orders = Orders::get($api2cartConnection->bridge_api_key, $params);

        if ($orders === null) {
            Log::warning('API2CART: Could not fetch orders');

            return;
        }

        info('API2CART: Imported orders', ['count' => count($orders)]);

        $this->saveOrders($api2cartConnection, $orders);

        Heartbeat::query()->updateOrCreate([
            'code' => implode('_', ['api2cart', 'ImportOrdersJob', $api2cartConnection->getKey()]),
        ], [
            'error_message' => 'Web orders not fetched for last hour',
            'expires_at' => now()->addHour(),
        ]);

        // for better performance and no long blocking jobs
        // recursively dispatch another import job
        // if there might be still some more to import
        if (count($orders) >= $batchSize) {
            self::dispatch($api2cartConnection);
        }
    }

    private function saveOrders(Api2cartConnection $api2cartConnection, array $ordersCollection): void
    {
        foreach ($ordersCollection as $order) {
            Api2cartOrderImports::query()->updateOrCreate([
                'connection_id' => $api2cartConnection->getKey(),
                'api2cart_order_id' => data_get($order, 'id'),
                'when_processed' => null,
            ], [
                'raw_import' => $order,
            ]);

            $this->updateLastSyncedTimestamp($api2cartConnection, $order);
        }
    }

    private function updateLastSyncedTimestamp(Api2cartConnection $connection, $order)
    {
        if (empty($order)) {
            return;
        }

        $lastTimeStamp = Carbon::createFromFormat(
            $order['modified_at']['format'],
            $order['modified_at']['value']
        );

        $connection->update([
            'last_synced_modified_at' => $lastTimeStamp->addSecond(),
        ]);
    }
}
