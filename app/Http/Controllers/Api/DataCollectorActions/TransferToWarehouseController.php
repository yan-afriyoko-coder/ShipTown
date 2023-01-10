<?php

namespace App\Http\Controllers\Api\DataCollectorActions;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollectionResource;
use App\Models\DataCollection;
use App\Modules\DataCollector\src\DataCollectorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferToWarehouseController extends Controller
{
    public function store(Request $request): DataCollectionResource
    {
        $destinationDataCollection = null;

        DB::transaction(function () use ($request, &$destinationDataCollection) {
            $sourceDataCollection = DataCollection::findOrFail($request->get('data_collector_id'));

            $sourceDataCollection->delete();

            $destinationDataCollection = DataCollectorService::transferScannedTo(
                $sourceDataCollection,
                $request->get('destination_warehouse_id')
            );
        });

        return DataCollectionResource::make($destinationDataCollection);
    }
}
