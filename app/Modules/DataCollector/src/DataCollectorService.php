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
    public static function runAction(DataCollection $dataCollector, $action)
    {
        DB::beginTransaction();

        if ($action === 'transfer_in_scanned') {
            $dataCollector->records()
                ->where('quantity_scanned', '!=', DB::raw(0))
                ->each(function (DataCollectionRecord $record) {
                    self::transferIn($record);
                });
        }

        if ($action === 'transfer_out_scanned') {
            $dataCollector->records()
                ->where('quantity_scanned', '!=', DB::raw(0))
                ->each(function (DataCollectionRecord $record) {
                    self::transferOut($record);
                });
        }

        if ($action === 'auto_scan_all_requested') {
            $dataCollector->records()
                ->whereNotNull('quantity_requested')
                ->update(['quantity_scanned' => DB::raw('quantity_requested')]);
        }

        DB::commit();
    }

    private static function transferIn(DataCollectionRecord $record): DataCollectionRecord
    {
        $custom_unique_reference_id = implode(':', [
            'dataCollection', $record->data_collection_id, 'uuid', Guid::uuid4()->toString(),
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

        $record->update(['quantity_scanned' => 0]);

        return $record;
    }

    private static function transferOut(DataCollectionRecord $record): DataCollectionRecord
    {
        $inventory = Inventory::firstOrCreate([
            'warehouse_id' => $record->dataCollection->warehouse_id,
            'product_id' => $record->product_id
        ], []);

        $custom_unique_reference_id = implode(':', [
            'dataCollection', $record->data_collection_id, 'uuid', Guid::uuid4()->toString(),
        ]);

        InventoryService::adjustQuantity(
            $inventory,
            $record->quantity_scanned * -1,
            'data collection transfer out',
            $custom_unique_reference_id
        );

        $record->update(['quantity_scanned' => 0]);

        return $record;
    }
}
