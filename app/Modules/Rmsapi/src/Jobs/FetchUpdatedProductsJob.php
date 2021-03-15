<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Client as RmsapiClient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class FetchUpdatedProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var RmsapiConnection
     */
    private $rmsapiConnection;

    /**
     * @var UuidInterface
     */
    public $batch_uuid;

    /**
     * Create a new job instance.
     *
     * @param int $rmsapiConnectionId
     * @throws Exception
     */
    public function __construct(int $rmsapiConnectionId)
    {
        $this->rmsapiConnection = RmsapiConnection::find($rmsapiConnectionId);
        $this->batch_uuid = Uuid::uuid4();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        logger('Starting Rmsapi FetchUpdatedProductsJob', ['connection_id' => $this->rmsapiConnection->getKey()]);

        $params = [
            'per_page' => config('rmsapi.import.products.per_page'),
            'order_by'=> 'db_change_stamp:asc',
            'min:db_change_stamp' => $this->rmsapiConnection->products_last_timestamp,
        ];

        $response = RmsapiClient::GET($this->rmsapiConnection, 'api/products', $params);

        if ($response->getResult()) {
            $this->saveImportedProducts($response->getResult());

            ProcessImportedBatch::dispatch($this->batch_uuid);

            if (isset($response->asArray()['next_page_url'])) {
                FetchUpdatedProductsJob::dispatch($this->rmsapiConnection->getKey());
            }
        }

        info('Imported RMSAPI products', [
            'location_id' => $this->rmsapiConnection->location_id,
            'count' => $response->asArray()['total'],
        ]);
    }

    public function saveImportedProducts(array $productList)
    {
        // we will use the same time for all records to speed up process
        $time = now()->toDateTimeString();

        $productsCollection = collect($productList);

        $insertData = $productsCollection->map(function ($product) use ($time) {
            return [
                'connection_id' => $this->rmsapiConnection->getKey(),
                'batch_uuid' => $this->batch_uuid->toString(),
                'raw_import' => json_encode($product),
                'created_at' => $time,
                'updated_at' => $time,
            ];
        });

        // we will use insert instead of create as this is way faster
        // method of inputting bulk of records to database
        // be careful as this probably wont invoke event (not 100% sure)
        RmsapiProductImport::query()->insert($insertData->toArray());

        RmsapiConnection::find($this->rmsapiConnection->getKey())->update([
            'products_last_timestamp' => $productsCollection->last()['db_change_stamp']
        ]);
    }
}
