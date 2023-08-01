<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Heartbeat;
use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ImportProductsJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private RmsapiConnection $rmsConnection;

    public string $batch_uuid;

    public int $uniqueFor = 120;

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
        logger('RMSAPI Starting FetchUpdatedProductsJob', ['connection_id' => $this->rmsConnection->getKey()]);

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

                Log::warning('RMSAPI Failed product fetch', [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ]);

                return false;
            }

            if ($response->getResult()) {
                $this->saveImportedProducts($response->getResult());
            }

            Log::info('RMSAPI Downloaded products', [
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
                'sku'                   => $product['item_code'],
                'quantity_on_hand'      => $product['quantity_on_hand'],
                'quantity_on_order'     => $product['quantity_on_order'],
                'quantity_available'    => $product['quantity_available'],
                'quantity_committed'    => $product['quantity_committed'],
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
