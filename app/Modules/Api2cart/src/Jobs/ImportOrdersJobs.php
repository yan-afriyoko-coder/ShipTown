<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Api\Orders;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ImportOrdersJobs implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * @var bool
     */
    public bool $finishedSuccessfully;

    /**
     * @var Api2cartConnection
     */
    private Api2cartConnection $api2cartConnection;

    /**
     * Create a new job instance.
     *
     * @param Api2cartConnection $api2cartConnection
     */
    public function __construct(Api2cartConnection $api2cartConnection)
    {
        $this->api2cartConnection = $api2cartConnection;
        $this->finishedSuccessfully = false;
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        $this->importOrders($this->api2cartConnection);

        // finalize
        $this->finishedSuccessfully = true;
    }

    /**
     * @param Api2cartConnection $connection
     * @param $order
     */
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

    /**
     * @param Api2cartConnection $api2cartConnection
     *
     * @throws Exception
     */
    private function importOrders(Api2cartConnection $api2cartConnection): void
    {
        $batchSize = 100;

        // initialize params
        $params = [
            'params'         => 'force_all',
            'sort_by'        => 'modified_at',
            'sort_direction' => 'asc',
            'count'          => $batchSize,
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

        if (!$orders) {
            $this->queueData(['count' => 0]);
            info('Imported Api2cart orders', ['count' => 0]);

            return;
        }

        $this->queueData(['fetched_count' => count($orders)]);

        $this->saveOrders($api2cartConnection, $orders);

        // for better performance and no long blocking jobs
        // recursively dispatch another import job
        // if there might be still some more to import
        if (count($orders) >= $batchSize) {
            self::dispatch($api2cartConnection);
        }

        info('Imported Api2cart orders', ['count' => count($orders)]);
    }

    /**
     * @param Api2cartConnection $api2cartConnection
     * @param array              $ordersCollection
     */
    private function saveOrders(Api2cartConnection $api2cartConnection, array $ordersCollection): void
    {
        foreach ($ordersCollection as $order) {
            Api2cartOrderImports::query()->create([
                'connection_id' => $api2cartConnection->getKey(),
                'raw_import' => $order,
            ]);

            $this->updateLastSyncedTimestamp($api2cartConnection, $order);
        }
    }
}
