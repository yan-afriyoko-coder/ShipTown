<?php

namespace App\Jobs\Rmsapi;

use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Client as RmsapiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var RmsapiConnection
     */
    private $rmsapiConnection = null;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $batch_uuid;

    /**
     * Create a new job instance.
     *
     * @param RmsapiConnection $rmsapiConnection
     * @throws \Exception
     */
    public function __construct(RmsapiConnection $rmsapiConnection)
    {
        $this->rmsapiConnection = $rmsapiConnection;
        $this->batch_uuid = Uuid::uuid4();
        logger('Job Rmsapi\ImportProductsJob dispatched');
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $params = [
            'per_page' => config('rmsapi.import.products.per_page'),
            'order_by'=> 'db_change_stamp:asc',
            'min:db_change_stamp' => $this->rmsapiConnection->products_last_timestamp,
        ];

        $response = RmsapiClient::GET(
            $this->rmsapiConnection,
            'api/products',
            $params);

        $productList = collect($response->getResult());

        // we will use the same time for all records to speed up process
        $time = now()->toDateTimeString();

        $insertProductList = new Collection();

//        // every record is carefully prepared
//        foreach ($productList as $product) {
//            $insertProductList->add([
//                'connection_id' => $this->rmsapiConnection->id,
//                'batch_uuid' => $this->batch_uuid->toString(),
//                'raw_import' => json_encode($product),
//                'created_at' => $time,
//                'updated_at' => $time,
//            ]);
//        }

        $insertProductList = $productList->map(function ($product) use ($time) {
            return [
                'connection_id' => $this->rmsapiConnection->id,
                'batch_uuid' => $this->batch_uuid->toString(),
                'raw_import' => json_encode($product),
                'created_at' => $time,
                'updated_at' => $time,
            ];
        });

        // we will use insert instead of create as this is way faster
        // method of inputting bulk of records to database
        // be careful as this probably wont invoke event (not 100% sure)
        RmsapiProductImport::query()->insert($insertProductList->toArray());

        $this->rmsapiConnection->update([
            'products_last_timestamp' => $productList->last()['db_change_stamp']
        ]);

        if($productList->isNotEmpty()) {
            ProcessImportedProductsJob::dispatch($this->batch_uuid);
        }

        if(isset($response->asArray()['next_page_url'])) {
            ImportProductsJob::dispatch($this->rmsapiConnection);
        }

    }
}
