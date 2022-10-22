<?php

namespace App\Modules\DataCollector\src;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
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

    public static function transferOutScanned(DataCollection $dataCollection)
    {
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
    }

    public static function transferScannedTo($dataCollection, $warehouse_id)
    {
        // create collection
        $destinationDataCollection = $dataCollection->replicate();

        DB::transaction(function () use ($warehouse_id, $dataCollection, $destinationDataCollection) {
            /** @var DataCollection $dataCollection */
            $destinationDataCollection->warehouse_id = $warehouse_id;
            $destinationDataCollection->name = implode('', [
                'Transfer from ',
                $dataCollection->warehouse->name,
                ' - ',
                $dataCollection->name
            ]);
            $destinationDataCollection->save();

            // copy records
            DB::statement(
                '
                INSERT INTO data_collection_records (data_collection_id, product_id, quantity_requested, created_at, updated_at)
                SELECT ?, product_id, quantity_scanned, now(), now()
                FROM data_collection_records
                WHERE data_collection_id = ?',
                [$destinationDataCollection->id, $dataCollection->id]
            );

            DataCollectorService::transferOutScanned($dataCollection);
        });

        return $destinationDataCollection;
    }
}
