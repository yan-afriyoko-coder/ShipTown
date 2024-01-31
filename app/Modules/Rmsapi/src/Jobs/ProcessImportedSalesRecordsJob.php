<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\InventoryMovement;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessImportedSalesRecordsJob extends UniqueJob
{
    public function handle(): bool
    {
        $batch_size = 50;

        do {
            $this->processImportedRecords($batch_size);

            Log::info('RMSAPI ProcessImportedSalesRecordsJob Processed imported sales records', [
                'count' => $batch_size,
            ]);

            $hasNoRecordsToProcess = RmsapiSaleImport::query()
                ->whereNull('reserved_at')
                ->whereNull('processed_at')
                ->whereNotNull('inventory_id')
                ->whereNotNull('product_id')
                ->whereNotNull('warehouse_id')
                ->where('comment', 'not like', 'PM_OrderProductShipment_%')
                ->exists();

            usleep(100000); // 0.1 sec
        } while ($hasNoRecordsToProcess);

        return true;
    }

    private function processImportedRecords(int $batch_size): void
    {
        $reservationTime = now();

        $ids = RmsapiSaleImport::query()
            ->whereNull('reserved_at')
            ->whereNull('processed_at')
            ->whereNotNull('inventory_id')
            ->whereNotNull('product_id')
            ->whereNotNull('warehouse_id')
            ->where('comment', 'not like', 'PM_OrderProductShipment_%')
            ->orderBy('id')
            ->limit($batch_size)
            ->pluck('id');

        RmsapiSaleImport::query()
            ->whereIn('id', $ids)
            ->whereNull('reserved_at')
            ->update(['reserved_at' => $reservationTime]);

        // process records
        $records = RmsapiSaleImport::query()
            ->whereIn('id', $ids)
            ->where('reserved_at', $reservationTime)
            ->whereNull('processed_at')
            ->orderBy('id')
            ->get();

        $inventoryMovements = $records
            ->map(function (RmsapiSaleImport $salesRecord) {
                return [
                    'inventory_id' => $salesRecord->inventory_id,
                    'custom_unique_reference_id' => $salesRecord->uuid,
                    'warehouse_code' => $salesRecord->warehouse->code,
                    'warehouse_id' => $salesRecord->warehouse_id,
                    'product_id' => $salesRecord->product_id,
                    'sequence_number' => null,
                    'occurred_at' => Carbon::createFromTimeString($salesRecord->transaction_time, 'Europe/Dublin')->utc(),
                    'type' => $salesRecord->type === 'rms_sale' ? 'sale' : 'adjustment',
                    'quantity_delta' => $salesRecord->quantity,
                    'description' => $salesRecord->type === 'rms_sale' ? 'rms_sale' : 'rmsapi_inventory_movement',
                    'updated_at' => now()->utc()->toDateTimeLocalString(),
                    'created_at' => now()->utc()->toDateTimeLocalString()
                ];
            });

        InventoryMovement::query()
            ->upsert($inventoryMovements->toArray(), ['custom_unique_reference_id'], [
                'inventory_id',
                'warehouse_code',
                'warehouse_id',
                'product_id',
                'sequence_number',
                'occurred_at',
                'type',
                'quantity_delta',
                'description',
                'updated_at',
            ]);

        RmsapiSaleImport::query()
            ->whereIn('id', $ids)
            ->where('reserved_at', $reservationTime)
            ->update(['processed_at' => now()->utc()->toDateTimeLocalString()]);
    }
}
