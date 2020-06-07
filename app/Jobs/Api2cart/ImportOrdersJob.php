<?php

namespace App\Jobs\Api2cart;

use App\Exceptions\Api2CartKeyNotSetException;
use App\Managers\CompanyConfigurationManager;
use App\Models\Api2cartOrderImports;
use App\Models\ConfigurationApi2cart;
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
            $lastSyncedTimeStamp = $this->getLastSyncedTimestamp();

            $ordersCollection = $this->fetchOrders($lastSyncedTimeStamp, 100);

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

        $config = ConfigurationApi2cart::query()->firstOrCreate([],[]);

        return $config['last_synced_modified_at'];

    }

    /**
     * @param mixed $timestamp
     * @param int $count
     * @param string $params
     * @return array
     * @throws Api2CartKeyNotSetException
     * @throws Exception
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
     */
    private function updateLastSyncedTimestamp(array $ordersCollection)
    {
        if (empty($ordersCollection)) {
            return;
        }

        $lastOrder = Arr::last($ordersCollection);

        ConfigurationApi2cart::query()->updateOrCreate([],[
            'last_synced_modified_at' => Carbon::createFromFormat(
                $lastOrder['modified_at']['format'],
                $lastOrder['modified_at']['value']
            )->addSecond()
        ]);
    }

}
