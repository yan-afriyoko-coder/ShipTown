<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataCollectorStoreRequest;
use App\Http\Resources\DataCollectionRecordResource;
use App\Models\DataCollectionRecord;
use App\Modules\Reports\src\Models\DataCollectionReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class DataCollectorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $report = new DataCollectionReport();

        $resource = $report->queryBuilder()
            ->orderByRaw('(data_collection_records.quantity_to_scan > 0) DESC, ' .
                '(data_collection_records.quantity_scanned > 0) DESC, shelf_location, product.sku')
            ->simplePaginate(request()->get('per_page', 10))
            ->appends(request()->query());

        return JsonResource::collection($resource);
    }

    public function store(DataCollectorStoreRequest $request): AnonymousResourceCollection
    {
        $attributes = $request->validated();

        /** @var DataCollectionRecord $dataCollectionRecord */
        $dataCollectionRecord = DataCollectionRecord::firstOrCreate([
            'product_id' => $attributes['product_id'],
        ]);

        $dataCollectionRecord->update([
            'quantity_scanned' => $dataCollectionRecord->quantity_scanned + $attributes['quantity_scanned'],
        ]);


        return DataCollectionRecordResource::collection([$dataCollectionRecord]);
    }
}
