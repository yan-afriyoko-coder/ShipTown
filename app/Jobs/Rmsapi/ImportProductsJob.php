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

    public $connection;

    /**
     * Create a new job instance.
     *
     * @param RmsapiConnection $connection
     */
    public function __construct(RmsapiConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $params = [
            'min:db_change_stamp' => $this->connection->products_last_timestamp,
//                'is_web_item' => 0,
            'per_page' => 100,
            'order_by'=> 'db_change_stamp:asc',
        ];

        $products = RmsapiClient::GET($this->connection, 'api/products', $params);

        foreach ($products->getResult() as $product) {

            RmsapiProductImport::query()->create([
               'connection_id' => $this->connection->id,
               'raw_import' => $product
            ]);

            $this->connection->update([
                'products_last_timestamp' => $product['db_change_stamp']
            ]);

        }

        if(isset($products->asArray()['next_page_url'])) {
            ImportProductsJob::dispatchNow($this->connection);
        }

    }
}
