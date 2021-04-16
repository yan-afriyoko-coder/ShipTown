<?php

namespace App\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class VerifyProductSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var Api2cartConnection
     */
    private $api2cartConnection = null;

    /**
     * @var array|null
     */
    private $product_data = null;

    /**
     * @var array
     */
    private $results = [];

    /**
     * Create a new job instance.
     *
     * @param Api2cartConnection $api2cartConnection
     * @param array $product_data
     */
    public function __construct(Api2cartConnection $api2cartConnection, array $product_data)
    {
        $this->api2cartConnection = $api2cartConnection;
        $this->product_data = $product_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $store_id = Arr::has($this->product_data, "store_id") ? $this->product_data["store_id"] : null;

        $product_now = Products::getProductInfo($this->api2cartConnection, $this->product_data["sku"]);

        if (empty($product_now)) {
            Log::warning("Update Check FAILED - Could not find product", ["sku" => $this->product_data["sku"]]);
            return;
        };

        $this->results = [
            "type" => $product_now["type"],
            "sku" => $product_now["sku"],
            "store_id" => $store_id,
            "differences" => $this->getDifferences($this->product_data, $product_now)
        ];

        if (empty($this->results["differences"])) {
            info('Update Check OK', $this->results);
        } else {
            Product::findBySKU($this->product_data['sku'])->attachTag('Not Synced');
            Log::warning("Update Check FAILED", ['differences' => $this->results, 'now' => $product_now]);
        }
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param array $expected
     * @param array $actual
     * @return array
     */
    private function getDifferences(array $expected, array $actual): array
    {
        // initialize variables
        $differences = [];

        $keys_to_verify = [
            "price",
            "special_price",
            "quantity"
        ];

        if ((Arr::has($actual, "manage_stock")) && ($actual["manage_stock"] != "False")) {
            $keys_to_verify = array_merge($keys_to_verify, ["quantity"]);
        }

        if ((Arr::has($expected, "sprice_expire")) &&
            (Carbon::createFromTimeString($expected["sprice_expire"])->isFuture())) {
            $keys_to_verify = array_merge($keys_to_verify, [
                "sprice_create",
                "sprice_expire",
            ]);
        }

        $expected_data = Arr::only($expected, $keys_to_verify);
        $actual_data   = Arr::only($actual, $keys_to_verify);

        foreach (array_keys($expected_data) as $key) {
            if ((!Arr::has($actual_data, $key)) or ($expected_data[$key] != $actual_data[$key])) {
                $differences[$key] = [
                    "expected" => $expected_data[$key],
                    "actual" => $actual_data[$key]
                ];
            }
        }

        return Arr::dot($differences);
    }
}
