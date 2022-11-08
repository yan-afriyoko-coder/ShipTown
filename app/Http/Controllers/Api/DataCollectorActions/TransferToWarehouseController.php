<?php

namespace App\Http\Controllers\Api\DataCollectorActions;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollectionResource;
use App\Models\DataCollection;
use App\Modules\DataCollector\src\DataCollectorService;
use Illuminate\Http\Request;

class TransferToWarehouseController extends Controller
{
    public function store(Request $request): DataCollectionResource
    {
        $sourceDataCollection = DataCollection::findOrFail($request->get('data_collector_id'));

        $destinationDataCollection = DataCollectorService::transferScannedTo(
            $sourceDataCollection,
            $request->get('destination_warehouse_id')
        );

        $sourceDataCollection->delete();

        return DataCollectionResource::make($destinationDataCollection);
    }
}
