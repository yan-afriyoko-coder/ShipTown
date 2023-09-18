<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Heartbeat;
use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ImportProductsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private RmsapiConnection $rmsConnection;

    public string $batch_uuid;

    public int $uniqueFor = 300;

    public function uniqueId(): string
    {
        return implode('-', [get_class($this), $this->rmsConnection->getKey()]);
    }

    public function __construct(int $rmsapiConnectionId)
    {
        $this->rmsConnection = RmsapiConnection::find($rmsapiConnectionId);
        $this->batch_uuid = Uuid::uuid4()->toString();
    }

    /**
     * Execute the job.
     *
     * @return boolean
     *
     * @throws Exception
     */
    public function handle(): bool
    {
        logger('RMSAPI Starting ImportProductsJob', ['connection_id' => $this->rmsConnection->getKey()]);

        $per_page = 500;
        $roundsLeft = 1000 / $per_page;

        do {
            $this->rmsConnection->refresh();

            $params = [
                'per_page'            => $per_page,
                'order_by'            => 'db_change_stamp:asc',
                'min:db_change_stamp' => $this->rmsConnection->products_last_timestamp,
            ];

            try {
                $response = RmsapiClient::GET($this->rmsConnection, 'api/products', $params);
            } catch (GuzzleException $e) {
                report($e);

                Log::warning('RMSAPI ImportProductsJob Failed product fetch', [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ]);

                return false;
            }

            if ($response->getResult()) {
                $this->saveImportedProducts($response->getResult());
            }

            Log::info('RMSAPI ImportProductsJob Downloaded products', [
                'warehouse_code' => $this->rmsConnection->location_id,
                'count'          => count($response->getResult()),
                'left'           => $response->asArray()['total'],
            ]);

            $roundsLeft--;
        } while ((isset($response->asArray()['next_page_url'])) && ($roundsLeft > 0));

        Heartbeat::query()->updateOrCreate([
            'code' => 'models_rmsapi_successful_fetch_warehouseId_'.$this->rmsConnection->location_id,
        ], [
            'error_message' => 'RMSAPI not synced for last hour WarehouseID: '.$this->rmsConnection->location_id,
            'expires_at' => now()->addHour()
        ]);

        return true;
    }

    /**
     * @throws Exception
     */
    public function saveImportedProducts(array $productList)
    {
        // we will use the same time for all records to speed up process
        $time = now()->toDateTimeString();

        $productsCollection = collect($productList);

        $insertData = $productsCollection->map(function ($product) use ($time) {
            return [
                'connection_id'         => $this->rmsConnection->getKey(),
                'warehouse_id'          => $this->rmsConnection->warehouse_id,
                'warehouse_code'        => $this->rmsConnection->location_id,
                'batch_uuid'            => $this->batch_uuid,
                'sku'                   => data_get($product, 'item_code', ''),
                'quantity_on_hand'      => data_get($product, 'quantity_on_hand', 0),
                'quantity_on_order'     => data_get($product, 'quantity_on_order', 0),
                'quantity_available'    => data_get($product, 'quantity_available', 0),
                'quantity_committed'    => data_get($product, 'quantity_committed', 0),
                'reorder_point'         => data_get($product, 'reorder_point', 0),
                'restock_level'         => data_get($product, 'restock_level', 0),
                'price'                 => data_get($product, 'price', 0),
                'cost'                  => data_get($product, 'cost', 0),
                'sale_price'            => data_get($product, 'sale_price', 0),
                'sale_price_start_date' => data_get($product, 'sale_price_start_date', '2000-01-01'),
                'sale_price_end_date'   => data_get($product, 'sale_price_end_date', '2000-01-01'),
                'is_web_item'           => data_get($product, 'is_web_item', false),
                'department_name'       => data_get($product, 'department_name', ''),
                'category_name'         => data_get($product, 'category_name', ''),
                'supplier_name'         => data_get($product, 'supplier_name', ''),
                'sub_description_1'     => data_get($product, 'sub_description_1', ''),
                'sub_description_3'     => data_get($product, 'sub_description_3', ''),
                'sub_description_2'     => data_get($product, 'sub_description_2', ''),
                'raw_import'            => json_encode($product),
                'created_at'            => $time,
                'updated_at'            => $time,
            ];
        });

        // we will use insert instead of create as this is way faster
        // method of inputting bulk of records to database
        // this won't invoke any events
        RmsapiProductImport::query()->insert($insertData->toArray());

        $this->rmsConnection->update(['products_last_timestamp' => $productsCollection->last()['db_change_stamp']]);

        retry(5, function () {
            DB::statement('
                UPDATE modules_rmsapi_products_imports
                LEFT JOIN products_aliases
                    ON modules_rmsapi_products_imports.sku = products_aliases.alias

                SET modules_rmsapi_products_imports.product_id = products_aliases.product_id

                WHERE modules_rmsapi_products_imports.product_id IS NULL
            ');
        }, 100);
    }
}
