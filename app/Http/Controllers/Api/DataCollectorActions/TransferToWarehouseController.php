<?php

namespace App\Http\Controllers\Api\DataCollectorActions;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollectionResource;
use App\Models\DataCollection;
use App\Models\Warehouse;
use App\Modules\DataCollector\src\DataCollectorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferToWarehouseController extends Controller
{
    public function store(Request $request): DataCollectionResource
    {
        $destinationDataCollection = null;

        DB::transaction(function () use ($request, &$destinationDataCollection) {
            $warehouse = Warehouse::query()->findOrFail($request->get('destination_warehouse_id'));
            $sourceDataCollection = DataCollection::findOrFail($request->get('data_collector_id'));

            $sourceDataCollection->update([
                'name' => implode('', ['Transfer To ', $warehouse->code, ' - ', $sourceDataCollection->name])
            ]);
            $sourceDataCollection->delete();

            $destinationDataCollection = DataCollectorService::transferScannedTo(
                $sourceDataCollection,
                $request->get('destination_warehouse_id')
            );
        });

        return DataCollectionResource::make($destinationDataCollection);
    }
}
