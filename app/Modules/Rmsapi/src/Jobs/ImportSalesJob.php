<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportSalesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var RmsapiConnection
     */
    private RmsapiConnection $rmsapiConnection;

    /**
     * Create a new job instance.
     *
     * @param int $rmsapiConnectionId
     *
     * @throws Exception
     */
    public function __construct(int $rmsapiConnectionId)
    {
        $this->rmsapiConnection = RmsapiConnection::query()->find($rmsapiConnectionId);
    }

    /**
     * Execute the job.
     *
     * @return boolean
     *
     */
    public function handle(): bool
    {
        Log::info('RMSAPI Starting FetchSalesJob', ['rmsapi_connection_id' => $this->rmsapiConnection->getKey()]);

        $params = [
            'per_page'            => 500,
            'order_by'            => 'db_change_stamp:asc',
            'min:db_change_stamp' => $this->rmsapiConnection->sales_last_timestamp,
        ];

        try {
            $response = RmsapiClient::GET($this->rmsapiConnection, 'api/transaction-entries', $params);
        } catch (GuzzleException $e) {
            Log::warning('RMSAPI Failed product fetch', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return false;
        }


        if ($response->getResult()) {
            $this->saveImportedRecords($response->getResult());
        }

//        Heartbeat::query()->updateOrCreate([
//            'code' => 'models_rmsapi_successful_fetch_warehouseId_'.$this->rmsapiConnection->location_id,
//        ], [
//            'error_message' => 'RMSAPI not synced for last hour WarehouseID: '.$this->rmsapiConnection->location_id,
//            'expires_at' => now()->addHour()
//        ]);
//
        Log::info('RMSAPI Downloaded transactions', [
            'warehouse_code' => $this->rmsapiConnection->location_id,
            'count'          => $response->asArray()['total'],
            ''
        ]);

        return true;
    }

    public function saveImportedRecords(array $records)
    {
        $time = now()->toDateTimeString();

        $recordsCollection = collect($records);

        $data = $recordsCollection->map(function ($record) use ($time) {
            return [
                'connection_id' => $this->rmsapiConnection->getKey(),
                'raw_import'    => json_encode($record),
                'created_at'    => $time,
                'updated_at'    => $time,
            ];
        });

        // for performance reasons we will use insert instead of create
        RmsapiSaleImport::query()->insert($data->toArray());

        RmsapiConnection::query()
            ->where(['id' => $this->rmsapiConnection->getKey()])
            ->update([
                'sales_last_timestamp' => $recordsCollection->last()['db_change_stamp'],
            ]);
    }
}
