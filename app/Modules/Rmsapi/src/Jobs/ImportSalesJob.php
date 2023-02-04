<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Heartbeat;
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
use Illuminate\Support\Str;

class ImportSalesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private RmsapiConnection $rmsapiConnection;

    public function __construct(int $rmsapiConnectionId)
    {
        $this->rmsapiConnection = RmsapiConnection::query()->find($rmsapiConnectionId);
    }

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
            Log::warning('RMSAPI Failed sales fetch', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return false;
        }

        if ($response->getResult()) {
            $this->saveImportedRecords($response->getResult());
        }

        Heartbeat::query()->updateOrCreate([
            'code' => 'models_rmsapi_successful_fetch_warehouseId_'.$this->rmsapiConnection->location_id,
        ], [
            'error_message' => 'RMSAPI not synced for last hour WarehouseID: '.$this->rmsapiConnection->location_id,
            'expires_at' => now()->addHour()
        ]);

        Log::info('RMSAPI Downloaded sales', [
            'warehouse_code' => $this->rmsapiConnection->location_id,
            'left'          => $response->asArray()['total'],
            ''
        ]);

        return true;
    }

    public function saveImportedRecords(array $salesRecords)
    {
        $time = now()->toDateTimeString();

        $recordsCollection = collect($salesRecords);

        $data = $recordsCollection->map(function ($saleRecord) use ($time) {
            $isImportedFromPM = Str::startsWith($saleRecord['comment'], ['PM_OrderProductShipment_']);

            return [
                'connection_id'         => $this->rmsapiConnection->getKey(),
                'sku'                   => $saleRecord['sku'],
                'price'                 => $saleRecord['price'],
                'quantity'              => $saleRecord['quantity'],
                'transaction_time'      => $saleRecord['transaction_time'],
                'transaction_number'    => $saleRecord['transaction_number'],
                'transaction_entry_id'  => $saleRecord['transaction_entry_id'],
                'comment'               => $saleRecord['comment'],
                'raw_import'            => json_encode($saleRecord),
                'reserved_at'           => $isImportedFromPM ? $time : null,
                'processed_at'          => $isImportedFromPM ? $time : null,
                'created_at'            => $time,
                'updated_at'            => $time,
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
