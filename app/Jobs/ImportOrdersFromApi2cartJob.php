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
        // initialize variables
        $params = [
            'params' => 'force_all',
            'sort_by' => 'modified_at',
            'sort_direction' => 'asc',
            'count' => 50,
            'modified_from' => $this->getLastSyncedTimestamp(),
        ];

        $api2cart_store_key = CompanyConfigurationManager::getBridgeApiKey();
        $orderToImportCollection = [];

        // pull orders
        $webOrdersCollection = Orders::getOrdersCollection($api2cart_store_key, $params);

        // transforms orders
        foreach ($webOrdersCollection['order'] as $order) {
            $orderToImportCollection[] = [
                'order_number' => $order['order_id'],
                'original_json' => $order,
                'products' => Arr::has($order, 'order_products')
                    ? $this->convertProducts($order['order_products'])
                    : [],
            ];
        }

        // save orders
        foreach ($orderToImportCollection as $order) {
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

    public function getLastSyncedTimestamp() {

        $config = ConfigurationApi2cart::query()->firstOrCreate([],[]);

        return $config['last_synced_modified_at'];

    }
}
