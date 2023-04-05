<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use App\Services\InventoryService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessImportedSalesRecordsJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $connection_id;

    public int $uniqueFor = 500;

    public function uniqueId(): string
    {
        return implode('-', [get_class($this), $this->connection_id]);
    }

    public function __construct(int $connection_id)
    {
        $this->connection_id = $connection_id;
    }

    public function handle()
    {
        $batch_size = 10;
        $maxRunCount = 100;

        do {
            $this->processImportedRecords($batch_size);

            $hasNoRecordsToProcess = ! RmsapiSaleImport::query()
                ->whereNull('reserved_at')
                ->whereNull('processed_at')
                ->exists();

            if ($hasNoRecordsToProcess) {
                return true;
            }

            $maxRunCount--;
        } while ($maxRunCount > 0);
    }

    private function processImportedRecords(int $batch_size): void
    {
        $reservationTime = now();

        RmsapiSaleImport::query()
            ->whereNull('reserved_at')
            ->where('connection_id', $this->connection_id)
            ->whereNull('processed_at')
            ->where('comment', 'not like', 'PM_OrderProductShipment_%')
            ->orderBy('id')
            ->limit($batch_size)
            ->update(['reserved_at' => $reservationTime]);

        // process records
        RmsapiSaleImport::query()
            ->where([
                'connection_id' => $this->connection_id,
                'reserved_at' => $reservationTime
            ])
            ->whereNull('processed_at')
            ->where('comment', 'not like', 'PM_OrderProductShipment_%')
            ->orderBy('id')
            ->get()
            ->each(function (RmsapiSaleImport $salesRecord) {
                try {
                    retry(3, function () use ($salesRecord) {
                        DB::transaction(function () use ($salesRecord) {
                            $this->import($salesRecord);
                        });
                    }, 1000);
                } catch (Exception $e) {
                    report($e);
                    Log::emergency($e->getMessage(), $e->getTrace());
                }
            });
    }

    private function import(RmsapiSaleImport $salesRecord)
    {
        $unique_reference_id = $salesRecord->uuid;

        $inventoryMovement = InventoryMovement::query()
            ->where('custom_unique_reference_id', $unique_reference_id)
            ->first();

        if ($inventoryMovement) {
            $salesRecord->update([
                'inventory_movement_id' => $inventoryMovement->getKey(),
                'processed_at' => now()
            ]);
            return;
        }

        $product = Product::query()
            ->whereHas('aliases', function ($join) use ($salesRecord) {
                $join->where('alias', $salesRecord->sku);
            })
            ->first();

        $inventory = Inventory::query()
            ->where('product_id', $product->getKey())
            ->where('warehouse_id', $salesRecord->rmsapiConnection->warehouse_id)
            ->orderByDesc('id')
            ->first();

        if ($salesRecord->type === 'rms_sale') {
            $inventoryMovement = InventoryService::sellProduct(
                $inventory,
                $salesRecord->quantity,
                $salesRecord->type,
                $unique_reference_id
            );
        } else {
            $inventoryMovement = InventoryService::adjustQuantity(
                $inventory,
                $salesRecord->quantity,
                $salesRecord->type,
                $unique_reference_id
            );
        }

        $salesRecord->update([
            'inventory_movement_id' => $inventoryMovement->getKey(),
            'processed_at' => now()
        ]);
    }
}
