<?php

namespace App\Modules\DataCollector\src\Services;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionStocktake;
use App\Models\DataCollectionTransferIn;
use App\Models\DataCollectionTransferOut;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Modules\DataCollector\src\Jobs\TransferInJob;
use App\Modules\DataCollector\src\Jobs\TransferOutJob;
use App\Modules\DataCollector\src\Jobs\TransferToJob;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataCollectorService
{
    public static function runAction(DataCollection $dataCollection, $action)
    {
        if ($action === 'transfer_in_scanned') {
            $dataCollection->update([
                'type' => DataCollectionTransferIn::class,
                'currently_running_task' => TransferInJob::class,
            ]);

            $dataCollection->delete();

            TransferInJob::dispatch($dataCollection->id);
            return;
        }

        if ($action === 'transfer_out_scanned') {
            $dataCollection->update([
                'type' => DataCollectionTransferOut::class,
                'currently_running_task' => TransferOutJob::class
            ]);

            $dataCollection->delete();

            TransferOutJob::dispatch($dataCollection->id);
            return;
        }

        if ($action === 'transfer_to_scanned') {
            $dataCollection->update([
                'type' => DataCollectionTransferOut::class,
                'currently_running_task' => TransferToJob::class
            ]);

            $dataCollection->delete();

            TransferToJob::dispatch($dataCollection->id);
            return;
        }

        if ($action === 'auto_scan_all_requested') {
            DB::transaction(function () use ($dataCollection) {
                $dataCollection->records()
                    ->whereNotNull('quantity_requested')
                    ->where('quantity_scanned', 0)
                    ->update(['quantity_scanned' => DB::raw('quantity_to_scan')]);
            });
            return;
        }

        if ($action === 'import_as_stocktake') {
            DB::transaction(function () use ($dataCollection) {
                self::importAsStocktake($dataCollection);
            });
        }
    }

    public static function transferScannedTo(DataCollection $sourceDataCollection, int $warehouse_id): DataCollection
    {
        $destinationDataCollection = null;

        DB::transaction(function () use ($warehouse_id, $sourceDataCollection, &$destinationDataCollection) {
            // create collection
            $destinationDataCollection = $sourceDataCollection->replicate(['destination_warehouse_id','currently_running_task','deleted_at']);
            $destinationDataCollection->type = DataCollectionTransferIn::class;
            $destinationDataCollection->warehouse_id = $warehouse_id;
            $destinationDataCollection->name = implode(' ', ['Transfer from', $sourceDataCollection->warehouse->code, '-', $sourceDataCollection->name]);
            $destinationDataCollection->save();

            $sourceDataCollection->update([
                'name' => implode(' ', ['Transfer To', $destinationDataCollection->warehouse->code, '-', $sourceDataCollection->name]),
                'type' => DataCollectionTransferOut::class,
                'currently_running_task' => TransferOutJob::class,
            ]);
            $sourceDataCollection->delete();

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

            TransferOutJob::dispatch($sourceDataCollection->getKey());
        });


        return $destinationDataCollection;
    }

    public static function importAsStocktake(DataCollection $dataCollection)
    {
        $dataCollection->update(['type' => DataCollectionStocktake::class]);

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

    public static function transferInRecord(DataCollectionRecord $record): void
    {
        Log::debug('TransferInJob transferring record', [$record->toArray()]);

        $inventory = Inventory::query()->firstOrCreate([
            'warehouse_id' => $record->dataCollection->warehouse_id,
            'product_id' => $record->product_id
        ], []);

        $custom_unique_reference_id = implode(':', [
            'data_collection_id', $record->data_collection_id,
            'data_collection_record_id', $record->getKey(),
            $record->updated_at
        ]);

        Log::debug('TransferInJob adjusting quantity', [
            'inventory' => $inventory->toArray(),
            'record' => $record->toArray(),
            'custom_unique_reference_id' => $custom_unique_reference_id
        ]);

        $record->refresh();

        if ($record->quantity_scanned == 0) {
            return;
        }

        DB::transaction(function () use ($record, $inventory, $custom_unique_reference_id) {
            try {
                $inventoryMovement = InventoryService::adjustQuantity(
                    $inventory,
                    $record->quantity_scanned,
                    'data collection transfer in',
                    $custom_unique_reference_id
                );

                $record->update([
                    'total_transferred_in' => $record->total_transferred_in + $record->quantity_scanned,
                    'quantity_scanned' => 0
                ]);
                Log::debug('TransferInJob adjusted quantity', ['inventoryMovement' => $inventoryMovement->toArray()]);
            } catch (\Exception $e) {
                Log::error('TransferInJob failed to adjust quantity (exception)', [$e->getMessage()]);
                if (!InventoryMovement::query()->where('custom_unique_reference_id', $custom_unique_reference_id)->exists()) {
                    report($e);
                    throw $e;
                }
            }
        });
    }

    /**
     * @param DataCollectionRecord $record
     */
    public static function transferOutRecord(DataCollectionRecord $record): void
    {
        DB::transaction(function () use ($record) {
            $inventory = Inventory::firstOrCreate([
                'warehouse_id' => $record->dataCollection->warehouse_id,
                'product_id' => $record->product_id
            ], []);

            $custom_unique_reference_id = implode(':', [
                'data_collection_id' , $record->data_collection_id,
                'data_collection_record_id' , $record->getKey(),
                $record->updated_at
            ]);

            try {
                InventoryService::adjustQuantity(
                    $inventory,
                    $record->quantity_scanned * -1,
                    'data collection transfer out',
                    $custom_unique_reference_id
                );
            } catch (\Exception $e) {
                if (! InventoryMovement::query()->where('custom_unique_reference_id', $custom_unique_reference_id)->exists()) {
                    report($e);
                    throw $e;
                }
            }

            $record->update([
                'total_transferred_out' => $record->total_transferred_out + $record->quantity_scanned,
                'quantity_scanned' => 0
            ]);
        });
    }
}
