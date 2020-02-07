<?php

namespace App\Jobs;

use App\Exceptions\Api2CartKeyNotSetException;
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
    public function __construct($user, $api2cart_store_key)
    {
        $this->user = $user;
        $this->api2cart_store_key = $api2cart_store_key;

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

        $orders = $content["result"]["order"];

        $products_to_reserve = [];

        foreach ($orders as $order) {

            foreach ($order["order_products"] as $product) {

                $products_to_reserve[] = [
                    "sku" => $product["model"],
                    "name" => $product["name"],
                    "quantity" => $product["quantity"],
                ];

            }

        }

        Product::withoutGlobalScope(AuthenticatedUserScope::class)
            ->where("user_id", $this->user->id)
            ->where("quantity_reserved", ">", 0)
            ->update(["quantity_reserved" => 0]);

        Inventory::query()
            ->where('location_id','=',999)
            ->update([
                "quantity_reserved" => 0
            ]);

        foreach ($products_to_reserve as $product) {

            $aProduct = Product::withoutGlobalScope(AuthenticatedUserScope::class)
                ->where("user_id", $this->user->id)
                ->where("sku", $product["sku"])
                ->first();

            if($aProduct) {
                $aProduct->increment("quantity_reserved", $product["quantity"]);

                $inventory = Inventory::query()->firstOrCreate([
                    'product_id' => $product->id,
                    'location_id' => 999
                ]);

                $inventory->increment("quantity_reserved", $product["quantity"]);
            }

        };

    }
}
