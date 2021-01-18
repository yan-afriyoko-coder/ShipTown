<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Orders;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

class FetchUpdatedOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var bool
     */
    public $finishedSuccessfully;

    /**
     * @var Api2cartConnection
     */
    private $api2cartConnection;

    /**
     * Create a new job instance.
     * @param Api2cartConnection $api2cartConnection
     */
    public function __construct(Api2cartConnection $api2cartConnection)
    {
        $this->api2cartConnection = $api2cartConnection;
        $this->finishedSuccessfully = false;
        logger('Job Api2cart\ImportOrdersJob dispatched');
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
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
            'last_synced_modified_at' => $lastTimeStamp->addSecond()
        ]);
    }

    /**
     * @param Api2cartConnection $connection
     * @throws Exception
     */
    private function importOrders(Api2cartConnection $connection): void
    {
        $batchSize = 100;

        // initialize params
        $params = [
            'params' => 'force_all',
            'sort_by' => 'modified_at',
            'sort_direction' => 'asc',
            'count' => $batchSize,
        ];

        if (isset($connection->last_synced_modified_at)) {
            $params = Arr::add(
                $params,
                'modified_from',
                $connection->last_synced_modified_at
            );
        }

        $orders = Orders::get($connection->bridge_api_key, $params);

        if (!$orders) {
            info('Imported Api2cart orders', ['count' => 0]);
            return;
        }

        $this->saveOrders($connection, $orders);

        // for better performance and no long blocking jobs
        // recursively dispatch another import job
        // if there might be still some more to import
        if (count($orders) >= $batchSize) {
            self::dispatch($connection);
        }

        info('Imported Api2cart orders', ['count' => count($orders)]);
    }

    /**
     * @param Api2cartConnection $connection
     * @param array $ordersCollection
     */
    private function saveOrders(Api2cartConnection $connection, array $ordersCollection): void
    {
        foreach ($ordersCollection as $order) {
            Api2cartOrderImports::query()->create([
                'raw_import' => $order
            ]);

            $this->updateLastSyncedTimestamp($connection, $order);
        }
    }
}
