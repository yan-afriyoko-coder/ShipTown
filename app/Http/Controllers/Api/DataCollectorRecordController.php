<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataCollectorStoreRequest;
use App\Http\Resources\DataCollectionRecordResource;
use App\Models\DataCollectionRecord;
use App\Models\ProductAlias;
use App\Modules\Reports\src\Models\DataCollectionReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class DataCollectorRecordController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $report = new DataCollectionReport();

        $resource = $report->queryBuilder()
            ->orderByRaw('
                data_collection_records.is_fully_scanned ASC,
                data_collection_records.is_requested DESC,
                data_collection_records.is_over_scanned DESC,
                shelf_location ASC,
                data_collection_records.quantity_to_scan DESC
            ')
            ->simplePaginate(request()->get('per_page', 10))
            ->appends(request()->query());

        return JsonResource::collection($resource);
    }

    public function store(DataCollectorStoreRequest $request): DataCollectionRecordResource
    {
        $record = $request->validated();

        if (isset($record['product_sku'])) {
            $record['product_id'] = ProductAlias::where('alias', $record['product_sku'])
                ->first('product_id')->product_id;

            unset($record['product_sku']);
        }

        $collectionRecord = null;

        DB::transaction(function () use ($record, &$collectionRecord) {
            /** @var DataCollectionRecord $collectionRecord */
            $collectionRecord = DataCollectionRecord::firstOrCreate([
                'data_collection_id' => $record['data_collection_id'],
                'product_id' => $record['product_id'],
            ]);

            $toUpdate = [];

            if (array_key_exists('quantity_scanned', $record)) {
                $toUpdate['quantity_scanned'] = $collectionRecord->quantity_scanned + $record['quantity_scanned'];
            }

            if (array_key_exists('quantity_requested', $record)) {
                $toUpdate['quantity_requested'] = $record['quantity_requested'];
            }

            $collectionRecord->update($toUpdate);
        });

        return DataCollectionRecordResource::make($collectionRecord);
    }
}
