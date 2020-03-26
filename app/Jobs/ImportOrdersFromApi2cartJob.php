<?php

namespace App\Jobs;

use App\Managers\CompanyConfigurationManager;
use App\Models\Order;
use App\Modules\Api2cart\Orders;
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
        $params = [
            'params' => 'force_all'
        ];

        $api2cart_store_key = CompanyConfigurationManager::getBridgeApiKey();

        $ordersCollection = Orders::getOrdersCollection($api2cart_store_key, $params);

        foreach ($ordersCollection['order'] as $order) {

            $newOrder = [
                'order_number' => $order['order_id'],
                'originalJson' => $order,
                'products' => Arr::has($order, 'order_products')
                    ? $this->convertProducts($order['order_products'])
                    : [],
            ];

            Order::query()->updateOrCreate(
                [
                    "order_number" => $newOrder['order_number'],
                ],
                array_merge(
                    $newOrder,
                    ['order_as_json' => $newOrder]
                )
            );
        }

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
}
