<?php

namespace App\Jobs\Api2cart;

use App\Exceptions\Api2CartKeyNotSetException;
use App\Managers\CompanyConfigurationManager;
use App\Models\Api2cartOrderImports;
use App\Models\Api2CartConnection;
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
        do {
            $connections = Api2CartConnection::query()->first();

            $ordersCollection = $this->fetchOrders($connections, 100);

            foreach ($ordersCollection as $order) {

                Api2cartOrderImports::query()->create([
                    'raw_import' => $order
                ]);

            }

            $this->updateLastSyncedTimestamp($ordersCollection);

        // keep going until we import all
        } while (count($ordersCollection) > 0);

        ProcessImportedOrdersJob::dispatch();

        // finalize
        $this->finishedSuccessfully = true;
    }

    /**
     * @return mixed
     */
    public function getLastSyncedTimestamp() {

        $config = Api2CartConnection::query()->firstOrCreate([],[]);

        return $config['last_synced_modified_at'];

    }

    /**
     * @param Api2CartConnection $connection
     * @param int $count
     * @param string $params
     * @return array
     * @throws Api2CartKeyNotSetException
     * @throws Exception
     */
    private function fetchOrders(Api2CartConnection $connection, int $count, string $params = 'force_all'){

        // initialize params
        $params = [
            'params' => $params,
            'sort_by' => 'modified_at',
            'sort_direction' => 'asc',
            'count' => $count,
        ];

        if(isset($connection->last_synced_modified_at)) {
            $params = Arr::add($params, 'modified_from', $connection->last_synced_modified_at);
        }

        return Orders::getOrdersCollection($connection->bridge_api_key, $params);
    }


    /**
     * @param array $ordersCollection
     */
    private function updateLastSyncedTimestamp(array $ordersCollection)
    {
        if (empty($ordersCollection)) {
            return;
        }

        $lastOrder = Arr::last($ordersCollection);

        $lastTimeStamp = Carbon::createFromFormat(
            $lastOrder['modified_at']['format'],
            $lastOrder['modified_at']['value']
        );

        Api2CartConnection::query()->updateOrCreate([],[
            'last_synced_modified_at' => $lastTimeStamp->addSecond()
        ]);
    }

}
