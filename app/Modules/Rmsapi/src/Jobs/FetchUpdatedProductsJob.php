<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Jobs\Modules\Rmsapi\ProcessImportedProductsJob;
use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Client as RmsapiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;

class FetchUpdatedProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var RmsapiConnection
     */
    private $rmsapiConnectionId = null;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    public $batch_uuid;

    /**
     * Create a new job instance.
     *
     * @param int $rmsapiConnectionId
     * @throws \Exception
     */
    public function __construct(int $rmsapiConnectionId)
    {
        $this->rmsapiConnectionId = $rmsapiConnectionId;
        $this->batch_uuid = Uuid::uuid4();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $rmsapiConnection = RmsapiConnection::find($this->rmsapiConnectionId);

        $params = [
            'per_page' => config('rmsapi.import.products.per_page'),
            'order_by'=> 'db_change_stamp:asc',
            'min:db_change_stamp' => $rmsapiConnection->products_last_timestamp,
        ];

        $response = RmsapiClient::GET(
            $rmsapiConnection,
            'api/products',
            $params
        );

        // early exit if nothing new imported
        if (empty($response->getResult())) {
            return;
        }

        $this->saveImportedProducts($response->getResult());

        ProcessImportedProductsJob::dispatch($this->batch_uuid);

        info('Imported RMSAPI products', [
            'location_id' => $rmsapiConnection->location_id,
            'count' => $response->asArray()['total'],
        ]);

        if (isset($response->asArray()['next_page_url'])) {
            FetchUpdatedProductsJob::dispatchNow($this->rmsapiConnectionId);
        }
    }

    public function saveImportedProducts(array $productList)
    {
        // we will use the same time for all records to speed up process
        $time = now()->toDateTimeString();

        $productsCollection = collect($productList);

        $insertData = $productsCollection->map(function ($product) use ($time) {
            return [
                'connection_id' => $this->rmsapiConnectionId,
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

        RmsapiConnection::find($this->rmsapiConnectionId)->update([
            'products_last_timestamp' => $productsCollection->last()['db_change_stamp']
        ]);
    }
}
