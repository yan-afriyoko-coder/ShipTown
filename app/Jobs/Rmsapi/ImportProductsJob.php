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
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $rmsapiConnection = null;

    /**
     * Create a new job instance.
     *
     * @param RmsapiConnection $connection
     */
    public function __construct(RmsapiConnection $rmsapiConnection)
    {
        $this->rmsapiConnection = $rmsapiConnection;
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
        $batch_uuid = Uuid::uuid4();

        $params = [
            'per_page' => config('rmsapi.import.products.per_page'),
            'order_by'=> 'db_change_stamp:asc',
            'min:db_change_stamp' => $this->rmsapiConnection->products_last_timestamp,
        ];

        $response = RmsapiClient::GET($this->rmsapiConnection, 'api/products', $params);

        $collection = collect($response->getResult());

        foreach ($collection as $product) {

            RmsapiProductImport::query()->create([
               'connection_id' => $this->rmsapiConnection->id,
               'batch_uuid' => $batch_uuid,
               'raw_import' => $product
            ]);

            $this->rmsapiConnection->update([
                'products_last_timestamp' => $product['db_change_stamp']
            ]);

        }

        if($collection->isNotEmpty()) {
            ProcessImportedProductsJob::dispatch($batch_uuid);
        }

        if(isset($response->asArray()['next_page_url'])) {
            ImportProductsJob::dispatch($this->rmsapiConnection);
        }

    }
}
