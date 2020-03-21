<?php

namespace App\Jobs;

use App\Exceptions\Api2CartKeyNotSetException;
use App\Managers\CompanyConfigurationManager;
use App\Models\Inventory;
use App\Models\Product;
use App\Scopes\AuthenticatedUserScope;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class JobImportOrderApi2Cart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $api2cart_store_key;
    private $callback;
    private $auth;
    private $api2cart_app_key;

    /**
     * Create a new job instance.
     *
     * @param $user Authenticatable
     * @param $api2cart_store_key
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->api2cart_store_key = CompanyConfigurationManager::getBridgeApiKey();

        $this->api2cart_app_key = env('API2CART_API_KEY', "");
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Api2CartKeyNotSetException
     */
    public function handle()
    {
        if(empty($this->api2cart_store_key)) {
            throw new Api2CartKeyNotSetException();
        }

        if(empty($this->api2cart_app_key)) {
            throw new Api2CartKeyNotSetException();
        }

        logger('Retrieving orders from API2CART');

        $guzzle = new \GuzzleHttp\Client([
            'base_uri' =>  'https://api.api2cart.com/v1.1/',
            'timeout' => 60,
            'exceptions' => true,
        ]);


        $response = $guzzle->get(
            'order.list.json',
            [
                'query' => [
                    "api_key" => $this->api2cart_app_key,
                    "store_key" => $this->api2cart_store_key,
                    "count" => 999,
                    "params" => "force_all",
                    "order_status" => "Processing",
                ]
            ]
        );


        $content = $response->getBody()->getContents();

        $content = json_decode($content, true);

        logger('Retrieved orders from API2CART', [
            "count" => count($content)
        ]);

        $orders = $content["result"]["order"];

        $products_to_reserve = [];

        foreach ($orders as $order) {

            logger('Order', [
               "order_id" => $order["order_id"],
               "products_count" => count($order["order_products"])
            ]);

            foreach ($order["order_products"] as $product_to_reserve) {

                $products_to_reserve[] = [
                    "sku" => $product_to_reserve["model"],
                    "name" => $product_to_reserve["name"],
                    "quantity" => $product_to_reserve["quantity"],
                ];

            }

        }

        logger('Clearing quantity_reserved');

        Product::query()
            ->where("quantity_reserved", ">", 0)
            ->update(["quantity_reserved" => 0]);

        logger('Clearing quantity_reserved in inventory');

        Inventory::query()
            ->where('location_id','=',999)
            ->update([
                "quantity_reserved" => 0
            ]);

        logger('Updating quantity_reserved', [
            "product_count" => count($products_to_reserve)
        ]);

        foreach ($products_to_reserve as $product_to_reserve) {

            logger('Retrieving product');

            $product = Product::query()
                ->where("sku", $product_to_reserve["sku"])
                ->first();

            if($product) {

                logger('Incrementing quantity_reserved');

                $product->increment("quantity_reserved", $product_to_reserve["quantity"]);

                logger('Fetching inventory record');

                $inventory = Inventory::query()->firstOrCreate([
                    'product_id' => $product->id,
                    'location_id' => 999
                ]);

                logger('Incrementing quantity_reserved in inventory');

                $inventory->increment("quantity_reserved", $product_to_reserve["quantity"]);
            }

        };

        logger("Finished job");

    }
}
