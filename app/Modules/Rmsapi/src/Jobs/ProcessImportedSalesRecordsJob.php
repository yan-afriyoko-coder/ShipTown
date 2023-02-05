<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\ProductPrice;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use App\Services\InventoryService;
use App\Traits\IsMonitored;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessImportedSalesRecordsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    private int $connection_id;

    public function __construct(int $connection_id)
    {
        $this->connection_id = $connection_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        RmsapiSaleImport::query()
            ->whereNull('processed_at')
            ->where('comment', 'like', 'PM_OrderProductShipment_%')
            ->update(['reserved_at' => now(), 'processed_at' => now()]);

        $maxRunCount = 3;

        do {
            $this->processImportedRecords(200);
            $maxRunCount--;
        } while ($maxRunCount > 0 and RmsapiSaleImport::query()->whereNull('processed_at')->exists());
    }

    private function processImportedRecords(int $batch_size): void
    {
        $reservationTime = now();

        RmsapiSaleImport::query()
            ->where('connection_id', $this->connection_id)
            ->where('comment', 'not like', 'PM_OrderProductShipment_%')
            ->orderBy('id')
            ->limit($batch_size)
            ->update(['reserved_at' => $reservationTime]);

        $records = RmsapiSaleImport::query()
            ->where([
                'connection_id' => $this->connection_id,
                'reserved_at' => $reservationTime
            ])
            ->whereNull('processed_at')
            ->where('comment', 'not like', 'PM_OrderProductShipment_%')
            ->orderBy('id')
            ->get();

        $records->each(function (RmsapiSaleImport $salesRecord) {
            try {
                retry(3, function () use ($salesRecord) {
                    $this->import($salesRecord);
                }, 100);
            } catch (Exception $e) {
                report($e);
            }
        });
    }

    private function import(RmsapiSaleImport $salesRecord)
    {
        $salesRecord->update(['processed_at' => now()]);
    }
}
