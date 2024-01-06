<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionStocktake;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportAsStocktakeJob extends UniqueJob
{
    public int $dataCollection_id;

    public function __construct(int $dataCollection_id)
    {
        $this->dataCollection_id = $dataCollection_id;
    }

    public function uniqueId(): string
    {
        return implode('-', [get_class($this), $this->dataCollection_id]);
    }

    public function handle(): void
    {
        /** @var DataCollection $sourceDataCollection */
        $dataCollection = DataCollection::withTrashed()->findOrFail($this->dataCollection_id);

        $inventoryMovementRecords = $dataCollection->records()
            ->where('quantity_scanned', '!=', DB::raw(0))
            ->get()
            ->map(function (DataCollectionRecord $record) use ($dataCollection) {
                /** @var Inventory $inventory */
                $inventory = Inventory::where([
                    'product_id' => $record->product_id,
                    'warehouse_id' => $dataCollection->warehouse_id,
                ])
                    ->first();

                $quantityDelta = $record->quantity_scanned - $inventory->quantity;

                $custom_uuid = implode('-', ['source_data_collections_records_id', $record->getKey()]);

                return [
                    'custom_unique_reference_id' => $custom_uuid,
                    'occurred_at' => now()->utc()->toDateTimeLocalString(),
                    'inventory_id' => $inventory->id,
                    'type' => InventoryMovement::TYPE_STOCKTAKE,
                    'product_id' => $inventory->product_id,
                    'warehouse_id' => $inventory->warehouse_id,
                    'quantity_before' => $inventory->quantity,
                    'quantity_delta' => $quantityDelta,
                    'quantity_after' => $inventory->quantity + $quantityDelta,
                    'description' => Str::substr('Data Collection - ' . $dataCollection->name, 0, 50),
                    'user_id' => Auth::id(),
                    'created_at' => now()->utc()->toDateTimeLocalString(),
                    'updated_at' => now()->utc()->toDateTimeLocalString(),
                ];
            });

        DB::transaction(function () use ($dataCollection, $inventoryMovementRecords) {
            $dataCollection->update(['type' => DataCollectionStocktake::class]);

            $dataCollection->delete();

            InventoryMovement::query()->insert($inventoryMovementRecords->toArray());
        });
    }
}
