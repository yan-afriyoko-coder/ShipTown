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
     */
    public function handle()
    {
        $params = [
            'per_page' => config('rmsapi.import.products.per_page'),
            'order_by'=> 'db_change_stamp:asc',
            'min:db_change_stamp' => $this->rmsapiConnection->products_last_timestamp,
        ];

        $products = RmsapiClient::GET($this->rmsapiConnection, 'api/products', $params);

        foreach ($products->getResult() as $product) {

            RmsapiProductImport::query()->create([
               'connection_id' => $this->rmsapiConnection->id,
               'raw_import' => $product
            ]);

            $this->rmsapiConnection->update([
                'products_last_timestamp' => $product['db_change_stamp']
            ]);

        }

        ProcessImportedProductsJob::dispatch();

        if(isset($products->asArray()['next_page_url'])) {
            ImportProductsJob::dispatch($this->rmsapiConnection);
        }

    }
}
