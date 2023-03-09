<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Heartbeat;
use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
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

    private RmsapiConnection $rmsConnection;

    public function __construct(int $rmsapiConnectionId)
    {
        $this->rmsConnection = RmsapiConnection::query()->find($rmsapiConnectionId);
    }

    public function handle(): bool
    {
        Log::info('RMSAPI Starting FetchSalesJob', ['rmsapi_connection_id' => $this->rmsConnection->getKey()]);

        $per_page = 500;
        $roundsLeft = 1000 / $per_page;

        do {
            $this->rmsConnection->refresh();

            $params = [
                'per_page'            => $per_page,
                'order_by'            => 'db_change_stamp:asc',
                'min:db_change_stamp' => $this->rmsConnection->sales_last_timestamp,
            ];

            try {
                $response = RmsapiClient::GET($this->rmsConnection, 'api/transaction-entries', $params);
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

            Log::info('RMSAPI Downloaded sales', [
                'warehouse_code' => $this->rmsConnection->location_id,
                'count'          => count($response->getResult()),
                'left'           => $response->asArray()['total'],
            ]);

            $roundsLeft--;
        } while ((isset($response->asArray()['next_page_url'])) && ($roundsLeft > 0));

        Heartbeat::query()->updateOrCreate([
            'code' => 'modules_rmsapi_successful_sales_fetch_warehouseId_'.$this->rmsConnection->location_id,
        ], [
            'error_message' => 'RMSAPI Sales not synced for last hour WarehouseID: '.$this->rmsConnection->location_id,
            'expires_at' => now()->addHour()
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
                'connection_id'         => $this->rmsConnection->getKey(),
                'uuid'                  => $saleRecord['uuid'],
                'type'                  => $saleRecord['type'],
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

        $this->rmsConnection->update(['sales_last_timestamp' => $recordsCollection->last()['db_change_stamp']]);
    }
}
