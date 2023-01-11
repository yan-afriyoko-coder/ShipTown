<?php

namespace App\Modules\DataCollector\src;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionTransferIn;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Modules\DataCollector\src\Jobs\TransferOutJob;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Guid\Guid;

class DataCollectorService
{
    public static function runAction(DataCollection $dataCollection, $action)
    {
        if ($action === 'transfer_in_scanned') {
            DB::transaction(function () use ($dataCollection) {
                self::transferInScanned($dataCollection);
            });
            return;
        }

        if ($action === 'transfer_out_scanned') {
            TransferOutJob::dispatch($dataCollection->id);
            return;
        }

        if ($action === 'auto_scan_all_requested') {
            DB::transaction(function () use ($dataCollection) {
                $dataCollection->records()
                    ->whereNotNull('quantity_requested')
                    ->update(['quantity_scanned' => DB::raw('quantity_requested')]);
            });
            return;
        }

        if ($action === 'import_as_stocktake') {
            DB::transaction(function () use ($dataCollection) {
                self::importAsStocktake($dataCollection);
            });
            return;
        }
    }

    public static function transferInScanned(DataCollection $dataCollection)
    {
        $dataCollection->update(['type' => DataCollectionTransferIn::class]);

        $dataCollection->delete();

        $dataCollection->records()
            ->where('quantity_scanned', '!=', DB::raw(0))
            ->each(function (DataCollectionRecord $record) {
                $custom_unique_reference_id = implode(':', [
                    'dataCollection',
                    $record->data_collection_id,
                    'uuid',
                    Guid::uuid4()->toString(),
                ]);

                $inventory = Inventory::firstOrCreate([
                    'warehouse_id' => $record->dataCollection->warehouse_id,
                    'product_id' => $record->product_id
                ], []);

                InventoryService::adjustQuantity(
                    $inventory,
                    $record->quantity_scanned,
                    'data collection transfer in',
                    $custom_unique_reference_id
                );

                $record->update([
                    'total_transferred_in' => $record->total_transferred_in + $record->quantity_scanned,
                    'quantity_scanned' => 0
                ]);
            });
    }

    public static function transferScannedTo(DataCollection $sourceDataCollection, int $warehouse_id): DataCollection
    {
        // create collection
        $destinationDataCollection = $sourceDataCollection->replicate(['deleted_at']);

        DB::transaction(function () use ($warehouse_id, $sourceDataCollection, $destinationDataCollection) {
            $destinationDataCollection->type = DataCollectionTransferIn::class;
            $destinationDataCollection->warehouse_id = $warehouse_id;
            $destinationDataCollection->name = implode('', [
                'Transfer from ',
                $sourceDataCollection->warehouse->code,
                ' - ',
                $destinationDataCollection->name,
                ''
            ]);
            $destinationDataCollection->save();

            $sourceDataCollection->records()
                ->where('quantity_scanned', '!=', DB::raw(0))
                ->get()
                ->each(function (DataCollectionRecord $record) use ($destinationDataCollection) {
                    $destinationDataCollectionRecord = $record->replicate(['quantity_to_scan']);
                    $destinationDataCollectionRecord->data_collection_id = $destinationDataCollection->id;
                    $destinationDataCollectionRecord->quantity_requested = $record->quantity_scanned;
                    $destinationDataCollectionRecord->quantity_scanned = 0;
                    $destinationDataCollectionRecord->save();
                });

            TransferOutJob::dispatch($sourceDataCollection->id);
        });

        return $destinationDataCollection;
    }

    public static function importAsStocktake(DataCollection $dataCollection)
    {
        $dataCollection->delete();

        $dataCollection->records()
            ->where('quantity_scanned', '!=', DB::raw(0))
            ->get()
            ->each(function (DataCollectionRecord $record) use ($dataCollection) {
                /** @var Inventory $inventory */
                $inventory = Inventory::where([
                        'product_id' => $record->product_id,
                        'warehouse_id' => $dataCollection->warehouse_id,
                    ])
                    ->first();

                $quantityDelta = $record->quantity_scanned - $inventory->quantity;

                /** @var InventoryMovement $inventoryMovement */
                $inventoryMovement = InventoryMovement::query()->create([
                    'inventory_id' => $inventory->id,
                    'product_id' => $inventory->product_id,
                    'warehouse_id' => $inventory->warehouse_id,
                    'quantity_before' => $inventory->quantity,
                    'quantity_delta' => $quantityDelta,
                    'quantity_after' => $inventory->quantity + $quantityDelta,
                    'description' => 'stocktake',
                    'user_id' => Auth::id(),
                ]);

                $inventory->update([
                    'quantity' => $inventoryMovement->quantity_after,
                    'last_counted_at' => now()
                ]);
            });
    }

    /**
     * @param DataCollectionRecord $record
     */
    public static function transferOutRecord(DataCollectionRecord $record): void
    {
        $custom_unique_reference_id = implode(':', [
            'dataCollection',
            $record->data_collection_id,
            'uuid',
            Guid::uuid4()->toString(),
        ]);

        $inventory = Inventory::firstOrCreate([
            'warehouse_id' => $record->dataCollection->warehouse_id,
            'product_id' => $record->product_id
        ], []);

        InventoryService::adjustQuantity(
            $inventory,
            $record->quantity_scanned * -1,
            'data collection transfer out',
            $custom_unique_reference_id
        );

        $record->update([
            'total_transferred_out' => $record->total_transferred_out + $record->quantity_scanned,
            'quantity_scanned' => 0
        ]);
    }
}
