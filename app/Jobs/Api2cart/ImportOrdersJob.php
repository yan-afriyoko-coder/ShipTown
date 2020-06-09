<?php

namespace App\Jobs\Api2cart;

use App\Exceptions\Api2CartKeyNotSetException;
use App\Managers\CompanyConfigurationManager;
use App\Models\Api2CartOrderImportsToRemove;
use App\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Orders;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

class ImportOrdersJob implements ShouldQueue
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
        info('Job ImportOrdersJob dispatched');
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        foreach (Api2cartConnection::all() as $connection) {
            $this->importOrders($connection);
        }

        ProcessImportedOrdersJob::dispatch();

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
        do {

            // initialize params
            $params = [
                'params' => 'force_all',
                'sort_by' => 'modified_at',
                'sort_direction' => 'asc',
                'count' => 100,
            ];

            if(isset($connection->last_synced_modified_at)) {
                $params = Arr::add($params, 'modified_from', $connection->last_synced_modified_at);
            }

            $orders = Orders::get($connection->bridge_api_key, $params);

            if (empty($orders)) {
                break;
            }

            $this->saveOrders($connection, $orders);

            // keep going until we import all
        } while (count($orders) > 0);
    }

    /**
     * @param Api2cartConnection $connection
     * @param array $ordersCollection
     */
    private function saveOrders(Api2cartConnection $connection, array $ordersCollection): void
    {
        foreach ($ordersCollection as $order) {

            Api2CartOrderImportsToRemove::query()->create([
                'raw_import' => $order
            ]);

            $this->updateLastSyncedTimestamp($connection, $order);

        }
    }

}
