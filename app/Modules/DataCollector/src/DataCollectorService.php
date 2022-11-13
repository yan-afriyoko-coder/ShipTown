<?php

namespace App\Modules\DataCollector\src;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionTransferIn;
use App\Models\DataCollectionTransferOut;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Guid\Guid;

class DataCollectorService
{
    public static function runAction(DataCollection $dataCollection, $action)
    {
        DB::beginTransaction();

        if ($action === 'transfer_in_scanned') {
            self::transferInScanned($dataCollection);
        }

        if ($action === 'transfer_out_scanned') {
            self::transferOutScanned($dataCollection);
        }

        if ($action === 'auto_scan_all_requested') {
            $dataCollection->records()
                ->whereNotNull('quantity_requested')
                ->update(['quantity_scanned' => DB::raw('quantity_requested')]);
        }

        DB::commit();
    }

    public static function transferInScanned(DataCollection $dataCollection)
    {
        $dataCollection->update(['type' => DataCollectionTransferIn::class]);

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

            $dataCollection->delete();
    }

    public static function transferOutScanned(DataCollection $dataCollection)
    {
        $dataCollection->update(['type' => DataCollectionTransferOut::class]);

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
                    $record->quantity_scanned * -1,
                    'data collection transfer out',
                    $custom_unique_reference_id
                );

                $record->update([
                    'total_transferred_out' => $record->total_transferred_out + $record->quantity_scanned,
                    'quantity_scanned' => 0
                ]);
            });

        $dataCollection->delete();
    }

    public static function transferScannedTo(DataCollection $sourceDataCollection, int $warehouse_id): DataCollection
    {
        // create collection
        $destinationDataCollection = $sourceDataCollection->replicate();

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

//            // copy records
//            DB::statement(
//                '
//                INSERT INTO data_collection_records (data_collection_id, product_id, quantity_requested, created_at, updated_at)
//                SELECT ?, product_id, quantity_scanned, now(), now()
//                FROM data_collection_records
//                WHERE quantity_scanned > 0 AND data_collection_id = ?',
//                [$destinationDataCollection->id, $sourceDataCollection->id]
//            );

            DataCollectorService::transferOutScanned($sourceDataCollection);
        });

        return $destinationDataCollection;
    }
}
